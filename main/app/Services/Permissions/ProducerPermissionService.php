<?php

namespace App\Services\Permissions;

use App\Events\UserPermissionsSynchronized;
use App\Models\Permission;
use App\Models\Producer;
use App\Models\User;

class ProducerPermissionService
{
	/**
	 * @param array $permissionIds
	 * @param Producer $producer
	 * @param User $user
	 * @return \Illuminate\Support\Collection
	 */
	public function syncUserPermissions(array $permissionIds, Producer $producer, User $user)
	{
		/** @var User $authUser */
		$authUser = auth('sanctum')->user();

		if (
			!$authUser->hasPermission(Permission::PERMISSION_PRODUCER_MANAGE_PERMISSIONS['name'], $producer->team) &&
			!$authUser->owns($producer->team)
		)
			throw new \LogicException("Доступ закрыт");

		if ($authUser->id === $user->id && $authUser->owns($producer->team))
			throw new \LogicException("Вы уже имеете все разрешения");

		if ($user->owns($producer->team))
			throw new \LogicException("Нельзя изменять права владельца");

		$permissions = Permission::whereIn('id', $permissionIds)->get();

		$user->syncPermissions($permissions, $producer->team);

		UserPermissionsSynchronized::dispatch(
			$user,
			$producer,
			$user->permissions()
				->where('team_id', $producer->id)
				->get()
		);

		return $permissions;
	}
}
