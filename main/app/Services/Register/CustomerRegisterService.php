<?php


namespace App\Services\Register;


use App\Contracts\Services\UserRegisterServiceContract;
use App\Http\Requests\Register\CustomerRegisterRequest;
use App\Jobs\SendEmailVerificationJob;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Laravel\Passport\ClientRepository;

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

			$clientRepository = new ClientRepository();
			$clientRepository->create(
				$user->id,
				$request->header('X-APP-TYPE'),'', null, true
			);

			DB::commit();
		} catch (\Throwable $e) {
			DB::rollBack();
			return response()->json([$e->getMessage(), $e->getCode()]);
		}
		SendEmailVerificationJob::dispatch($user)->afterResponse();

		return response()->json([$user]);
	}
}
