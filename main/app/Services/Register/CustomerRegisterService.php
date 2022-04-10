<?php


namespace App\Services\Register;

use App\Contracts\Services\UserRegisterServiceContract;
use App\Http\Requests\Register\CustomerRegisterRequest;
use App\Jobs\SendEmailVerificationJob;
use App\Models\User;
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
			/** @var User $user */
			$user = User::query()->create($request->validated());

			$token = $user->createToken($request->email)
				->plainTextToken;

			DB::commit();
		} catch (\Throwable $e) {
			DB::rollBack();
			return response()->json([
				$e->getMessage(),
				$e->getCode()
			]);
		}

		SendEmailVerificationJob::dispatch($user)
			->afterResponse();

		/** secure cookie does not show up in browser data */
		return response()->json([$user])
			->withCookie(cookie('token', $token, 0, null, null, true, true, false, 'none'));
	}
}
