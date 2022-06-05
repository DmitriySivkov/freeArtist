<?php


namespace App\Services\Register;


use App\Contracts\Services\RegisterServiceContract;
use App\Models\Producer;
use App\Models\ProducerUser;
use App\Models\Role;
use App\Traits\WithAuthUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProducerRegisterService implements RegisterServiceContract
{
	use WithAuthUser;

	/**
	 * @param array $producerData
	 * @return JsonResponse
	 * @throws \Throwable
	 */
	public function register($producerData)
	{
		DB::beginTransaction();
		try {
			$user = $this->resolveUserByAuthCookie();

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

		$producer->pivot = ProducerUser::where('user_id', $user->id)
			->whereJsonContains('rights', ProducerUser::RIGHT_OWNER)
			->first();

		return response()->json($producer);
	}
}
