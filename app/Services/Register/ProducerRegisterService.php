<?php


namespace App\Services\Register;


use App\Contracts\Services\UserRegisterServiceContract;
use App\Http\Requests\Register\ProducerRegisterRequest;
use App\Jobs\SendEmailVerificationJob;
use App\Models\Producer;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\ClientRepository;

class ProducerRegisterService implements UserRegisterServiceContract
{
	/**
	 * @param ProducerRegisterRequest $request
	 * @return JsonResponse
	 */
	public function run($request)
	{
		DB::beginTransaction();
		try {
			$validated = collect($request->validated());

			/** @var Producer $producer */
			if (!$validated->get('producer'))
				$producer = Producer::query()
					->create(['title' => $validated->get('new_producer_title')]);
			else
				$producer = Producer::query()
					->find($validated->get('producer')['value']);

			/** @var User $user */
			$user = User::query()->create([
				'name' => $validated->get('name'),
				'email' => $validated->get('email'),
				'password' => $validated->get('password'),
				'role_id' => $validated->get('role_id'),
				'producer_id' => $producer->id,
			]);

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

		SendEmailVerificationJob::dispatch($user)->onQueue('SendEmailVefiricationQueue');

		return response()->json([$user, $producer]);
	}
}
