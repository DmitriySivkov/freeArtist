<?php


namespace App\Services;


use App\Contracts\TeamServiceContract;
use App\Contracts\UserServiceContract;
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
	 * @param $phone
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function loginWithCredentials($phone)
    {
		$isMobile = request()->input('is_mobile');

		$this->user = User::wherePhone($phone)->firstOrFail();

		$tokenName = $phone . '-' . ($isMobile ? 'mobile' : 'desktop');

		$this->revokeOldTokensOnCurrentDevice($tokenName);

		$tokenRaw = $this->user->createToken($tokenName);

		$token = $tokenRaw->plainTextToken;

		// double check user
		$this->user = User::whereHas('tokens',
			fn($query) => $query->where('id', $tokenRaw->accessToken->id)
		)->firstOrFail();

		Auth::loginUsingId($this->user->id);

		if ($isMobile) {
			return $this->makeResponse(['token' => $token]);
		}

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
		if (!$this->user = auth('sanctum')->user()) {
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
		$permissions = $this->user->permissions()
			->select([
				'permissions.id',
				'permissions.name',
				'permissions.display_name',
				'permissions.description',
				'teams.id as team_id'
			])
			->leftJoin('teams', 'permission_user.team_id', '=', 'teams.id')
			->get();

		$roles = $this->user->roles()
			->select([
				'roles.id',
				'roles.name',
				'roles.display_name',
				'roles.description',
				'teams.id as team_id'
			])
			->leftJoin('teams', 'role_user.team_id', '=', 'teams.id')
			->get();

		return response()->json([
			'user' => $this->user,
			'user_permissions' => $permissions,
			'user_roles' => $roles,
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
					'logo',
					'storefrontImage'
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
}
