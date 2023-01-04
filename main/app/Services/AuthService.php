<?php


namespace App\Services;


use App\Contracts\TeamServiceContract;
use App\Contracts\UserServiceContract;
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
		$this->user->load([
			'roles',
			'permissions'
		]);

		return response()->json([
			'user' => $this->user,
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
			$team->makeHidden('pivot');
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

		$incomingRequests = $userService->getUserIncomingRequests();
		$outgoingRequests = $userService->getUserOutgoingRequests();

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

}
