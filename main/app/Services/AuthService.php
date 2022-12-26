<?php


namespace App\Services;


use App\Models\Permission;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Services\RelationRequests\ProducerRelationRequestService;
use App\Services\RelationRequests\UserRelationRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
	protected ?User $user;
	protected Collection $userTeams;

	/**
	 * @param $credentials
	 * @param $isMobile
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function loginWithCredentials($credentials, $isMobile)
    {
		if (!Auth::attempt($credentials))
			return response()->json(['errors' => ['total' => ['Неверный телефон или пароль']]], 422);

		$this->user = User::where('phone', $credentials['phone'])->firstOrFail();

		$tokenName = $credentials['phone'] . '-' . ($isMobile ? 'mobile' : 'desktop');

		$this->revokeOldTokensOnCurrentDevice($tokenName);

		$token = $this->user->createToken($tokenName)->plainTextToken;

		if ($isMobile)
			return $this->makeResponse(['token' => $token]);

		$cookie = cookie(
			'token', $token, 0, "/", config('session.domain'),
			true, true, false, 'lax'
		);

		return $this->makeResponse()->withCookie($cookie);
    }

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function loginWithToken()
    {
        $this->user = auth("sanctum")->user();

		if (!$this->user)
			return response()->json(['errors' => ['total' => ['Пользователь не найден']]], 422);

		return $this->makeResponse();
    }

	/**
	 * @param array $additional
	 * @return \Illuminate\Http\JsonResponse
	 */
	private function makeResponse(array $additional = [])
	{
		$this->getUserPayload();

		return response()->json([
			'user' => $this->user,
			'user_producer' => $this->getUserProducerTeams()
		] + $additional);
	}

	/**
	 * @return void
	 */
	private function getUserPayload()
	{
		$this->user->load([
			'roles',
			'permissions'
		]);

		$urrService = new UserRelationRequestService;
		$urrService->setUser($this->user);
		$this->user->outgoing_coworking_requests = $urrService->getOutgoingCoworkingRequests();
	}

	/**
	 * @return array|Collection
	 */
	private function getUserProducerTeams()
	{
		$userProducerTeams = $this->user->rolesTeams()
			->where('role_id', Role::ROLE_PRODUCER['id'])
			->get()
			->makeHidden('pivot');

		if ($userProducerTeams->isEmpty())
			return [];

		$prrService = new ProducerRelationRequestService;

		return $userProducerTeams->map(function(Team $team) use ($prrService) {
			$prrService->setProducer($team->detailed);

			if (
				$this->user->owns($team) ||
				$this->user->hasPermission(Permission::PERMISSION_PRODUCER_INCOMING_COWORKING_REQUESTS['name'], $team->name)
			) {
				$requests = [
					'data' => [
						'incoming_coworking_requests' => $prrService->getIncomingCoworkingRequests()
					],
				];
				$requests['total_pending_request_count'] = collect($requests['data'])
					->reduce(function($carry, $requestList) {
						$carry += count($requestList);
						return $carry;
					}, 0);

				$team->requests = $requests;
			}

			return $team;
		});
	}

	/**
	 * @param $token
	 * @return void
	 */
	private function revokeOldTokensOnCurrentDevice($token)
	{
		$this->user->tokens()->where('name', $token)->delete();
	}


	/**
	 * @param array $userData
	 * @param bool $isMobile
	 * @return JsonResponse
	 * @throws \Throwable
	 */
	public function register($userData, $isMobile)
	{
		DB::beginTransaction();
		try {
			$unhashedPassword = $userData['password'];
			$userData['password'] = Hash::make($unhashedPassword);

			$user = User::create($userData);

			$response = $this->loginWithCredentials([
				'phone' => $user->phone,
				'password' => $unhashedPassword
			], $isMobile);

			DB::commit();
		} catch (\Throwable) {
			DB::rollBack();
			throw new \LogicException("Ошибка сервера регистрации");
		}

		return $response;
	}


}
