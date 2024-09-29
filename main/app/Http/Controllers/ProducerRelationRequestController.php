<?php

namespace App\Http\Controllers;

use App\Events\TeamAcceptUserRequest;
use App\Models\Permission;
use App\Models\Producer;
use App\Models\RelationRequest;
use App\Models\Role;
use App\Models\User;

class ProducerRelationRequestController
{
	public function index(Producer $producer)
	{
		return $producer->incomingRelationRequests()
			->with(['from'])
			->orderBy('created_at', 'desc')
			->get();
	}

	//todo - validation;
	public function accept(Producer $producer, RelationRequest $relationRequest)
	{
		/** @var User $user */
		$user = auth()->user();

		if (
			!$user->owns($producer->team) &&
			!$user->hasPermission(
				Permission::PERMISSION_PRODUCER_REQUESTS['name'],
				$producer->team->name
			)
		) {
			throw new \LogicException('Доступ закрыт');
		}

		if ($relationRequest->status !== RelationRequest::STATUS_PENDING) {
			throw new \LogicException('Заявка уже обработана');
		}

		/** @var User $userToAttach */
		$userToAttach = $relationRequest->from;

		if (!$userToAttach) {
			throw new \LogicException('Пользователь заблокирован или удалён');
		}

		try {
			\DB::beginTransaction();

			$userToAttach->attachRole(Role::ROLES[Role::PRODUCER], $producer->team);

			$relationRequest->status = RelationRequest::STATUS_ACCEPTED;

			$relationRequest->save();

			TeamAcceptUserRequest::dispatch($relationRequest, $userToAttach); // todo - check out

			\DB::commit();
		} catch (\Throwable $e) {
			\DB::rollBack();
			throw new \LogicException('Ошибка сервера заявок');
		}

		return $relationRequest;
	}

	public function reject(Producer $producer, RelationRequest $relationRequest)
	{
		/** @var User $user */
		$user = auth()->user();

		if (
			!$user->owns($producer->team) &&
			!$user->hasPermission(
				Permission::PERMISSION_PRODUCER_REQUESTS['name'],
				$producer->team->name
			)
		) {
			throw new \LogicException('Доступ закрыт');
		}

		if ($relationRequest->status !== RelationRequest::STATUS_PENDING) {
			throw new \LogicException('Заявка уже обработана');
		}

		$relationRequest->status = RelationRequest::STATUS_REJECTED_BY_RECIPIENT;

		$relationRequest->save();

		return $relationRequest;
	}
}
