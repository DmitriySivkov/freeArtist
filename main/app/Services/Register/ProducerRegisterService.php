<?php


namespace App\Services\Register;


use App\Contracts\Services\RegisterServiceContract;
use App\Models\Producer;
use App\Models\ProducerUser;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProducerRegisterService implements RegisterServiceContract
{

	/**
	 * @param array $producerData
	 * @return JsonResponse
	 * @throws \Throwable
	 */
	public function register($producerData)
	{
		DB::beginTransaction();
		try {
			/** @var User $user */
			$user = auth('sanctum')->user();

			$isUserOwner = ProducerUser::where('user_id', $user->id)
				->whereJsonContains('rights', ProducerUser::RIGHT_OWNER)
				->exists();

			if ($isUserOwner)
				throw new \LogicException('Вы уже являетесь владельцем');

			$producer = Producer::create([
					'title' => $producerData['producer']
				]);

			$user->producers()
				->attach($producer->id, ['rights' => [ProducerUser::RIGHT_OWNER]]);

			$user->roles()
				->attach([Role::PRODUCER]);

			DB::commit();
		} catch (\Throwable $e) {
			DB::rollBack();
			throw new \LogicException($e->getMessage());
		}

		return response()->json([
			'producer' => [
					'pivot' => ProducerUser::where('user_id', $user->id)
						->where('producer_id', $producer->id)
						->first()
						->makeHidden('id')
				] + $producer->toArray(),
			'role' => $user->roles()
				->where('role_id', Role::PRODUCER)
				->first()
		]);
	}
}
