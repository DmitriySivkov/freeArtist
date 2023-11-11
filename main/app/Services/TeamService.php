<?php

namespace App\Services;

use App\Contracts\ProducerServiceContract;
use App\Contracts\TeamServiceContract;
use App\Events\UserPermissionsSynchronized;
use App\Models\Permission;
use App\Models\Producer;
use App\Models\RelationRequest;
use App\Models\Role;
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

	public function acceptRequest(RelationRequest $relationRequest)
	{
		/** @var User $user */
		$user = auth()->user();

		if (
			!$user->owns($this->team) &&
			!$user->hasPermission(
				Permission::PERMISSION_PRODUCER_REQUESTS['name'],
				$this->team->name
			)
		)
			throw new \LogicException('Доступ закрыт');

		/** @var array $relationRequestStatus */
		$relationRequestStatus = $relationRequest->status;

		if ($relationRequestStatus['id'] !== RelationRequest::STATUS_PENDING['id'])
			throw new \LogicException('Заявка уже обработана');

		if ($this->team->detailed_type === Producer::class) {
			/** @var User $userToAttach */
			$userToAttach = $relationRequest->from;

			if (!$userToAttach)
				throw new \LogicException('Пользователь заблокирован или удалён');

			\DB::beginTransaction();
			try {
				$userToAttach->attachRole(Role::ROLES[Role::PRODUCER], $this->team);

				$relationRequest->update([
					'status' => RelationRequest::STATUS_ACCEPTED
				]);

				\DB::commit();
			} catch (\Throwable $e) {
				\DB::rollBack();
				throw new \LogicException('Ошибка сервера заявок');
			}
		}

		return $relationRequest->refresh();
	}

	public function rejectRequest(RelationRequest $relationRequest)
	{
		/** @var User $user */
		$user = auth()->user();

		if (
			!$user->owns($this->team) &&
			!$user->hasPermission(
				Permission::PERMISSION_PRODUCER_REQUESTS['name'],
				$this->team->name
			)
		)
			throw new \LogicException('Доступ закрыт');

		/** @var array $relationRequestStatus */
		$relationRequestStatus = $relationRequest->status;

		if ($relationRequestStatus['id'] !== RelationRequest::STATUS_PENDING['id'])
			throw new \LogicException('Заявка уже обработана');

		$relationRequest->update([
			'status' => RelationRequest::STATUS_REJECTED_BY_RECIPIENT['id']
		]);

		return $relationRequest->refresh();
	}
}
