<?php

namespace App\Services;

use App\Contracts\ProducerServiceContract;
use App\Contracts\TeamServiceContract;
use App\Events\UserPermissionsSynchronized;
use App\Models\Permission;
use App\Models\Producer;
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

	public function onAuth()
	{
		if ($this->team->detailed_type === Producer::class) {
			/** @var ProducerService $service */
			$service = app(ProducerServiceContract::class);
			$service->setProducer($this->team->detailed);
		}

		$data = $service->onAuth();

		foreach ($data as $prop => $value) {
			$this->team->setAttribute($prop, $value);
		}

		return $this->team;
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
		$authUser = auth('sanctum')->user();

		if (
			!$authUser->hasPermission(Permission::PERMISSION_PRODUCER_MANAGE_PERMISSIONS['name'], $team) &&
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
				->where('team_id', $team->id)
				->get()
		);

		return $permissions;
	}

	public function getTeamIncomingRequests()
	{
		if ($this->team->detailed_type === Producer::class) {
			/** @var ProducerService $producerService */
			$producerService = app(ProducerServiceContract::class);
			$producerService->setProducer($this->team->detailed);
			return $producerService->getIncomingRequests();
		}
		return [];
	}

	public function getTeamOutgoingRequests()
	{
		if ($this->team->detailed_type === Producer::class) {
			/** @var ProducerService $producerService */
			$producerService = app(ProducerServiceContract::class);
			$producerService->setProducer($this->team->detailed);
			return $producerService->getOutgoingRequests();
		}
		return [];
	}
}
