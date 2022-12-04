<?php


namespace App\Services;


use App\Models\Permission;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Services\RelationRequests\ProducerRelationRequestService;
use App\Services\RelationRequests\UserRelationRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
	protected ?User $user;
	protected Collection $userTeams;

	/**
	 * @param $credentials
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function loginWithCredentials($credentials)
    {
		if (!Auth::attempt($credentials))
			return response()->json(['errors' => ['total' => ['Неверный телефон или пароль']]], 422);

		$this->user = User::where('phone', $credentials['phone'])->firstOrFail();

		$token = $this->user->createToken($credentials['phone'] . '-' . request()->userAgent())->plainTextToken;

		$cookie = cookie(
			'token', $token, 0, "/", config('session.domain'),
			true, true, false, 'lax'
		);

		return $this->makeResponse(['token' => $token])->withCookie($cookie);
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
	 * @param $additional
	 * @return \Illuminate\Http\JsonResponse
	 */
	private function makeResponse($additional = [])
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

}
