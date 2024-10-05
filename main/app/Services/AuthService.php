<?php


namespace App\Services;

use App\Http\Resources\UserTeamResource;
use App\Models\Order;
use App\Models\Producer;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphTo;
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
	 * @param User|null $user
	 * @return void
	 */
	public function setUser(?User $user)
	{
		$this->user = $user;
	}

	/**
	 * @param $credentials
	 * @param $isMobile
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function loginWithCredentials($credentials, $isMobile)
	{
		// todo - move to request 'prepareForValidation'
		$credentials['phone'] = preg_replace('/[^0-9.]+/', '', $credentials['phone']);

		if (!Auth::attempt($credentials)) {
			return response()->json([
				'errors' => ['total' => ['Неверный телефон или пароль']]
			], 422);
		}

		/** @var User $user */
		$user = auth()->user();

		$this->setUser($user);

		$this->onCredentialsAuthAction();

		$tokenName = $this->user->phone . '-' . ($isMobile ? 'mobile' : 'desktop');

		$this->revokeOldTokensOnCurrentDevice($tokenName);

		$token = $this->user->createToken($tokenName)->plainTextToken;

		if ($isMobile) {
			return $this->makeResponse(['token' => $token]);
		}

		// todo - move to cookie helper (service)
		// todo - save cookie for longer period of time ?
		$cookie = cookie(
			'token', $token, 0, "/", config('session.domain'),
			true, true, false, 'lax'
		);

		return $this->makeResponse()->withCookie($cookie);
	}

	/**
	 * @return false|JsonResponse
	 */
	public function loginWithToken()
    {
		/** @var User|null $user */
		$user = auth('sanctum')->user();

		$this->setUser($user);

		if (!$this->user) {
			return false;
		}

		Auth::loginUsingId($this->user->id);

		return $this->makeResponse();
    }

	/**
	 * @param array $additional
	 * @return \Illuminate\Http\JsonResponse
	 */
	private function makeResponse(array $additional = [])
	{
		$userPermissionsByTeam = $this->user->permissions()
			->select(['id', 'name'])
			->get()
			->groupBy('pivot.team_id')
			->toArray();

		// todo - resource
		return response()->json([
			'user' => $this->user,
			'user_teams' => UserTeamResource::collection(
				$this->user->teams()
					->withPivot('role_id')
					->with(['detailed'])
					->get()
					->map(function(Team $team) use ($userPermissionsByTeam) {
						$team->permissions = \Arr::exists($userPermissionsByTeam, $team->id) ?
							$userPermissionsByTeam[$team->id] :
							[];

						return $team;
					})
			),
		] + $additional);
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

	private function onCredentialsAuthAction()
	{
		$uuid = json_decode(request()->cookie('orders'));

		if ($uuid) {
			Order::whereHas('transaction', fn($query) =>
				$query->where('uuid', $uuid)
			)
				->whereNull('user_id')
				->update([
					'user_id' => $this->user->id
				]);
		}
	}
}
