<?php

namespace App\Services;

use App\Contracts\ProducerServiceContract;
use App\Models\Permission;
use App\Models\Producer;
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
			!$user->hasPermission(Permission::PERMISSION_PRODUCER_LOGO['name'], $producer->team) &&
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
				Permission::PERMISSION_PRODUCER_REQUESTS['name'],
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
				Permission::PERMISSION_PRODUCER_REQUESTS['name'],
				$this->producer->team->name
			)
		) {
			return [];
		}

		return $this->producer->outgoingRelationRequests()->get();
	}
}