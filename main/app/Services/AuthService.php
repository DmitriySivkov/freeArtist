<?php


namespace App\Services;


use App\Contracts\TeamServiceContract;
use App\Contracts\UserServiceContract;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use App\Models\Order;
use App\Models\Producer;
use App\Models\Team;
use App\Models\User;
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
		// todo - resource
		return response()->json([
			'user' => $this->user,
			'user_permissions' => PermissionResource::collection(
				$this->user->permissions()->get()
			),
			'user_roles' => RoleResource::collection(
				$this->user->roles()->get()
			),
			'user_teams' => $this->getUserTeams(),
			'user_requests' => $this->getUserRequests(),
			'user_teams_requests' => $this->getTeamRequests()
		] + $additional);
	}

	/**
	 * @return array|Collection
	 */
	private function getUserTeams()
	{
		$this->userTeams = $this->user->rolesTeams()->get();

		if ($this->userTeams->isEmpty())
			return [];

		return $this->userTeams->map(function(Team $team) {
			$team->loadMorph('detailed', [
				Producer::class => [
					'city',
					'logo'
				]
			]);
			return $team;
		});
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	private function getUserRequests()
	{
		/** @var UserService $userService */
		$userService = app(UserServiceContract::class);
		$userService->setUser($this->user);

		$incomingRequests = $userService->getUserIncomingRequests()
			->get()
			->loadMorph('from', [
				Producer::class => ['team']
			]);

		$outgoingRequests = $userService->getUserOutgoingRequests()
			->get()
			->loadMorph('to', [
				Producer::class => ['team']
			]);

		return $incomingRequests->merge($outgoingRequests);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	private function getTeamRequests()
	{
		/** @var TeamService $teamService */
		$teamService = app(TeamServiceContract::class);

		return $this->userTeams->reduce(function(Collection $carry, Team $team) use ($teamService) {
			$teamService->setTeam($team);
			$carry = $carry->merge($teamService->getTeamIncomingRequests());
			$carry = $carry->merge($teamService->getTeamOutgoingRequests());
			return $carry;
		}, collect([]));
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
