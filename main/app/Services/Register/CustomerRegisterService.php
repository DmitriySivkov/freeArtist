<?php


namespace App\Services\Register;

use App\Contracts\Services\RegisterServiceContract;
use App\Models\Role;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerRegisterService implements RegisterServiceContract
{
	/**
	 * @param array $userData
	 * @return JsonResponse
	 * @throws \Throwable
	 */

	public function register($userData)
	{
		DB::beginTransaction();
		try {
			$unhashedPassword = $userData['password'];
			$userData['password'] = Hash::make($unhashedPassword);
			/** @var User $user */
			$user = User::create($userData);

			$response = (new AuthService())->loginWithCredentials(
				[
					'phone' => $user->phone,
					'password' => $unhashedPassword
				]
			);
			DB::commit();
		} catch (\Throwable $e) {
			DB::rollBack();
			throw new \LogicException("Ошибка сервера регистрации");
		}

		return $response;
	}
}
