<?php


namespace App\Services\Register;

use App\Contracts\Services\UserRegisterServiceContract;
use App\Http\Requests\Register\CustomerRegisterRequest;
use App\Jobs\SendEmailVerificationJob;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CustomerRegisterService implements UserRegisterServiceContract
{
	/**
	 * @param CustomerRegisterRequest $request
	 * @return JsonResponse
	 */
	public function run($request)
	{
		DB::beginTransaction();
		try {
			User::query()->create($request->validated());
			DB::commit();
		} catch (\Throwable $e) {
			DB::rollBack();
			return response()->json("Произошла ошибка регистрации")
				->setStatusCode(422);
		}

		return (new AuthService())->loginWithCredentials($request->only(['phone', 'password']));
	}
}
