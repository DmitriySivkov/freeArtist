<?php

namespace App\Services\RelationRequests;

use App\Models\Permission;
use App\Models\Producer;
use App\Models\RelationRequest;
use App\Models\Role;
use App\Models\User;

class ProducerRelationRequestService
{
	protected Producer $producer;

	/**
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getIncomingCoworkingRequests()
	{
		if (!$this->producer)
			throw new \LogicException('Изготовитель не задан');

		return $this->producer->incomingRelationRequests()
			->where('from_type', User::class)
			->get();
	}

	/**
	 * @param RelationRequest $relationRequest
	 * @return RelationRequest
	 * @throws \Throwable
	 */
	public function acceptCoworkingRequest(RelationRequest $relationRequest)
	{
		if (!$this->producer)
			throw new \LogicException('Изготовитель не задан');

		/** @var User $user */
		$user = auth('sanctum')->user();

		if (
			!$user->owns($this->producer->team) &&
			!$user->hasPermission(Permission::PERMISSION_PRODUCER_INCOMING_COWORKING_REQUESTS['name'], $this->producer->team->name)
		)
			throw new \LogicException('Доступ закрыт');

		/** @var array $relationRequestStatus */
		$relationRequestStatus = $relationRequest->status;

		if ($relationRequestStatus['id'] !== RelationRequest::STATUS_PENDING['id'])
			throw new \LogicException('Заявка уже обработана');

		/** @var User $userToAttach */
		$userToAttach = $relationRequest->from;
		if (!$userToAttach)
			throw new \LogicException('Пользователь заблокирован или удалён');

		\DB::beginTransaction();
		try {
			$userToAttach->attachRole(Role::ROLE_PRODUCER['name'], $this->producer->team);
			$relationRequest->update([
				'status' => RelationRequest::STATUS_ACCEPTED['id']
			]);

			\DB::commit();
		} catch (\Throwable $e) {
			\DB::rollBack();
			throw new \LogicException('Ошибка сервера запросов');
		}

		return $relationRequest->refresh();
	}

	/**
	 * @param RelationRequest $relationRequest
	 * @return RelationRequest
	 * @throws \Throwable
	 */
	public function rejectCoworkingRequest(RelationRequest $relationRequest)
	{
		if (!$this->producer)
			throw new \LogicException('Изготовитель не задан');

		/** @var User $user */
		$user = auth('sanctum')->user();

		if (
			!$user->owns($this->producer->team) &&
			!$user->hasPermission(Permission::PERMISSION_PRODUCER_INCOMING_COWORKING_REQUESTS['name'], $this->producer->team->name)
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

	/**
	 * @param Producer $producer
	 * @return void
	 */
	public function setProducer(Producer $producer)
	{
		$this->producer = $producer;
	}
}
