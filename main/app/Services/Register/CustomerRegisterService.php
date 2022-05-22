<?php


namespace App\Services\Register;

use App\Contracts\Services\UserRegisterServiceContract;
use App\Http\Requests\Register\CustomerRegisterRequest;
use App\Jobs\SendEmailVerificationJob;
use App\Models\Role;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CustomerRegisterService implements UserRegisterServiceContract
{
	/**
	 * @param array $userData
	 * @return JsonResponse
	 */

	public function run($userData)
	{
		DB::beginTransaction();
		try {
			/** @var User $user */
			$user = User::create($userData);
			$user->roles()->attach(Role::CUSTOMER);
			$response = (new AuthService())->loginWithCredentials(['phone' => '89109667968', 'password' => 'ye11owstorm1196']);
		} catch (\Throwable $e) {
			DB::rollBack();
			throw new \LogicException("Ошибка сервера регистрации");
		}
		DB::commit();
		return $response;
	}
}
