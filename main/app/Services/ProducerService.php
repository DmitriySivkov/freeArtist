<?php

namespace App\Services;

use App\Contracts\ProducerServiceContract;
use App\Models\Image;
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
	 * @return Image|\Illuminate\Database\Eloquent\Model
	 * @throws \Exception
	 */
	public function setLogo(Producer $producer)
	{
		try {
			/** @var User $user */
			$user = auth()->user();

			if (
				!$user->hasPermission(Permission::PERMISSION_PRODUCER_LOGO['name'], $producer->team) &&
				!$user->owns($producer->team)
			)
				throw new \Exception('Доступ закрыт');

			$basePath = 'team_' . $producer->team->id;

			if ($producer->logo) {
				Storage::disk('public')->delete($producer->logo->path);

				Image::find($producer->logo_id)->delete();
			}

			$path = Storage::disk('public')->putFileAs(
				$basePath,
				request()->file('logo'),
				'logo-' . now()->timestamp . '.' . request()->file('logo')->extension()
			);

			$image = Image::create([
				'imageable_id' => $producer->id,
				'imageable_type' => Producer::class,
				'path' => $path
			]);

			if (!$path)
				throw new \Exception('Ошибка сервиса');

			$producer->logo_id = $image->id;

			$producer->save();

		} catch (\Throwable $e) {
			throw new \Exception($e->getMessage());
		}

		return $image;
	}

	/**
	 * @param Producer $producer
	 * @return Image|\Illuminate\Database\Eloquent\Model
	 * @throws \Exception
	 */
	public function setStorefrontImage(Producer $producer)
	{
		try {
			/** @var User $user */
			$user = auth()->user();

			if (
				!$user->hasPermission(Permission::PERMISSION_PRODUCER_STOREFRONT['name'], $producer->team) &&
				!$user->owns($producer->team)
			)
				throw new \Exception('Доступ закрыт');

			$basePath = 'team_' . $producer->team->id;

			if ($producer->storefrontImage) {
				Storage::disk('public')->delete($producer->storefrontImage->path);

				Image::find($producer->storefront_image_id)->delete();
			}

			$path = Storage::disk('public')->putFileAs(
				$basePath,
				request()->file('storefront_image'),
				'storefront-' . now()->timestamp . '.' . request()->file('storefront_image')->extension()
			);

			$image = Image::create([
				'imageable_id' => $producer->id,
				'imageable_type' => Producer::class,
				'path' => $path
			]);

			if (!$path)
				throw new \Exception('Ошибка сервиса');

			$producer->storefront_image_id = $image->id;

			$producer->save();

		} catch (\Throwable $e) {
			throw new \Exception($e->getMessage());
		}

		return $image;
	}

	/**
	 * @param $producerData
	 * @return array
	 * @throws \Throwable
	 */
	public function register($producerData)
	{
		DB::beginTransaction();
		try {
			/** @var User $user */
			$user = auth()->user();

			if ($user->ownProducer()->exists())
				throw new \Exception('Вы уже являетесь изготовителем-владельцем');

			$producer = Producer::create([
				'city_id' => $producerData['city_id']
			]);

			$producer->paymentMethods()->attach(\App\Models\PaymentMethod::PAYMENT_METHOD_CASH_ID);

			$team = Team::create([
				'name' => 'producer_' . $user->id . '_owner',
				'display_name' => $producerData['display_name'],
				'description' => '',
				'user_id' => $user->id,
				'detailed_id' => $producer->id,
				'detailed_type' => Producer::class
			]);

			$user->roles()->attach(
				Role::PRODUCER,
				['user_type' => User::class, 'team_id' => $team->id]
			);

			DB::commit();
		} catch (\Throwable $e) {
			DB::rollBack();
			throw new \LogicException($e->getMessage());
		}

		return [
			'team' => $team->loadMorph('detailed', [
				Producer::class => 'city'
			]),
			'role' => [
				'id' => Role::PRODUCER,
				'team_id' => $team->id
			]
		];
	}

	/**
	 * @return array|\Illuminate\Database\Eloquent\Collection
	 */
	public function getIncomingRequests()
	{
		if (!$this->producer) {
			throw new \Exception('Изготовитель не задан');
		}

		/** @var User $user */
		$user = auth()->user();

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
			throw new \Exception('Изготовитель не задан');

		/** @var User $user */
		$user = auth()->user();

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
