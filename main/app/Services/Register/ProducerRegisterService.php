<?php


namespace App\Services\Register;


use App\Contracts\Services\RegisterServiceContract;
use App\Models\Producer;
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
				->attach($producer->id);

			$user->roles()
				->attach([Role::PRODUCER, Role::PRODUCER_OWNER]);

			DB::commit();
		} catch (\Throwable $e) {
			DB::rollBack();
			throw new \LogicException('Ошибка сервера регистрации');
		}

		return response()->json($producer);
	}
}
