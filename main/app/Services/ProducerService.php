<?php

namespace App\Services;

use App\Contracts\ProducerServiceContract;
use App\Models\Permission;
use App\Models\Producer;
use App\Models\RelationRequest;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProducerService implements ProducerServiceContract
{
	protected Producer $producer;

	/**
	 * @param Producer $producer
	 * @return void
	 */
	public function setProducer(Producer $producer)
	{
		$this->producer = $producer;
	}

	/**
	 * @param Producer $producer
	 * @return string
	 */
	public function setLogo(Producer $producer)
	{
		/** @var User $user */
		$user = auth('sanctum')->user();

		if (
			!$user->hasPermission(Permission::PERMISSION_PRODUCER_MANAGE_LOGO['name'], $producer->team) &&
			!$user->owns($producer->team)
		)
			throw new \LogicException('Доступ закрыт');

		$basePath = 'team_' . $producer->team->id . '/logo';

		if (Storage::disk('public')->exists($basePath))
			Storage::disk('public')->deleteDirectory($basePath);

		$path = Storage::disk('public')->putFile(
			$basePath,
			request()->file('logo'),
		);

		if (!$path)
			throw new \LogicException('Ошибка сервиса');

		$producer->logo = $path;
		$producer->save();

		return $path;
	}

	/**
	 * @param $producerData
	 * @return JsonResponse
	 * @throws \Throwable
	 */
	public function register($producerData)
	{
		DB::beginTransaction();
		try {
			/** @var User $user */
			$user = auth('sanctum')->user();

			if ($user->ownProducer()->exists())
				throw new \LogicException('Вы уже являетесь изготовителем-владельцем');

			$producer = Producer::create();

			$team = Team::create([
				'name' => 'producer_' . $user->id . '_owner',
				'display_name' => $producerData['display_name'],
				'description' => '',
				'user_id' => $user->id,
				'detailed_id' => $producer->id,
				'detailed_type' => Producer::class
			]);

			$user->roles()->attach(
				Role::ROLE_PRODUCER['id'],
				['user_type' => User::class, 'team_id' => $team->id]
			);

			DB::commit();
		} catch (\Throwable $e) {
			DB::rollBack();
			throw new \LogicException($e->getMessage());
		}

		return response()->json([
			'team' => $team,
			'role' => $user->roles()
				->where('role_id', Role::ROLE_PRODUCER['id'])
				->where('team_id', $team->id)
				->first()
		]);
	}

	/**
	 * @return array|\Illuminate\Database\Eloquent\Collection
	 */
	public function getIncomingRequests()
	{
		if (!$this->producer)
			throw new \LogicException('Изготовитель не задан');

		/** @var User $user */
		$user = auth('sanctum')->user();

		if (
			!$user->owns($this->producer->team) &&
			!$user->hasPermission(
				Permission::PERMISSION_PRODUCER_INCOMING_REQUESTS['name'],
				$this->producer->team->name
			)
		) {
			return [];
		}

		return $this->producer->incomingRelationRequests()->get();
	}

	/**
	 * @return array|\Illuminate\Database\Eloquent\Collection
	 */
	public function getOutgoingRequests()
	{
		if (!$this->producer)
			throw new \LogicException('Изготовитель не задан');

		/** @var User $user */
		$user = auth('sanctum')->user();

		if (
			!$user->owns($this->producer->team) &&
			!$user->hasPermission(
				Permission::PERMISSION_PRODUCER_OUTGOING_REQUESTS['name'],
				$this->producer->team->name
			)
		) {
			return [];
		}

		return $this->producer->outgoingRelationRequests()->get();
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
			!$user->hasPermission(
				Permission::PERMISSION_PRODUCER_INCOMING_REQUESTS['name'],
				$this->producer->team->name
			)
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
			throw new \LogicException('Ошибка сервера заявок');
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
			!$user->hasPermission(
				Permission::PERMISSION_PRODUCER_INCOMING_REQUESTS['name'],
				$this->producer->team->name
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
