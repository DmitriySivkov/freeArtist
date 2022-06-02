<?php


namespace App\Services\Register;


use App\Contracts\Services\UserRegisterServiceContract;
use App\Models\Producer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class ProducerRegisterService implements UserRegisterServiceContract
{

	/**
	 * @param array $producerData
	 * @return JsonResponse
	 * @throws \Throwable
	 */
	public function register($producerData)
	{
		$PAtoken = PersonalAccessToken::findToken(request()->cookie('token'));

		/** @var User $user */
		$user = $PAtoken->tokenable;

		DB::beginTransaction();
		try {
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
