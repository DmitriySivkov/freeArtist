<?php

namespace App\Services;

use App\Contracts\TeamServiceContract;
use App\Events\UserPermissionsSynchronized;
use App\Models\Permission;
use App\Models\Team;
use App\Models\User;

class TeamService implements TeamServiceContract
{
	protected Team $team;

	/**
	 * @param Team $team
	 * @return void
	 */
	public function setTeam(Team $team)
	{
		$this->team = $team;
	}

	/**
	 * @param array $permissionIds
	 * @param Team $team
	 * @param User $user
	 * @return \Illuminate\Support\Collection
	 */
	public function syncUserPermissions(array $permissionIds, Team $team, User $user)
	{
		/** @var User $authUser */
		$authUser = auth()->user();

		if (
			!$authUser->hasPermission(Permission::PERMISSION_PRODUCER_PERMISSIONS['name'], $team) &&
			!$authUser->owns($team)
		)
			throw new \LogicException("Доступ закрыт");

		if ($authUser->id === $user->id && $authUser->owns($team))
			throw new \LogicException("Вы уже имеете все разрешения");

		if ($user->owns($team))
			throw new \LogicException("Нельзя изменять права владельца");

		$permissions = Permission::whereIn('id', $permissionIds)->get();

		$user->syncPermissions($permissions, $team);

		UserPermissionsSynchronized::dispatch(
			$user,
			$team,
			$user->permissions()
				->select([
					'permissions.id',
					'permissions.name',
					'permissions.display_name',
					'permissions.description',
					'teams.id as team_id'
				])
				->where('permission_user.team_id', $team->id)
				->leftJoin('teams', 'permission_user.team_id', '=', 'teams.id')
				->get()
		);

		return $permissions;
	}
}
