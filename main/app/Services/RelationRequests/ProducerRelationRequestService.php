<?php

namespace App\Services\RelationRequests;

use App\Models\ProducerUser;
use App\Models\RelationRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProducerRelationRequestService
{
	public function getIncomingCoworkingRequests()
	{

	}

	/**
	 * @param RelationRequest $relationRequest
	 * @return RelationRequest
	 * @throws \Throwable
	 */
	public function acceptCoworkingRequest(RelationRequest $relationRequest)
	{
		$producerUser = ProducerUser::where('producer_id', $relationRequest->to_id)
			->where('user_id', $relationRequest->from_id);

		$from = User::find($relationRequest->to_id);

		DB::beginTransaction();
		try {
			if (!$producerUser->exists()) {
				ProducerUser::create([
					'producer_id' => $relationRequest->to_id,
					'user_id' => $relationRequest->from_id,
					'rights' => [ProducerUser::RIGHT_COWORKER],
					'user_active' => 1
				]);
			}
			else
				$producerUser->update([
					'rights' => collect($producerUser->rights)
						->push(ProducerUser::RIGHT_COWORKER)
				]);

			if (!$from->roles->contains(Role::PRODUCER))
				$from->roles()->attach(Role::PRODUCER);

			$relationRequest->update([
				'status' => RelationRequest::STATUS_ACCEPTED['id']
			]);

			DB::commit();
		} catch (\Throwable $e) {
			DB::rollBack();
			throw new \LogicException('Ошибка сервера запросов');
		}

		return $relationRequest;
	}

	/**
	 * @param RelationRequest $relationRequest
	 * @return RelationRequest
	 * @throws \Throwable
	 */
	public function rejectCoworkingRequest(RelationRequest $relationRequest)
	{
		DB::beginTransaction();
		try {
			$relationRequest->update([
				'status' => RelationRequest::STATUS_REJECTED_BY_RECIPIENT['id']
			]);
			DB::commit();
		} catch (\Throwable $e) {
			DB::rollBack();
			throw new \LogicException('Ошибка сервера запросов');
		}

		return $relationRequest;
	}
}
