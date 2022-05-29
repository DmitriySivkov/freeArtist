<?php


namespace App\Services\Register;


use App\Contracts\Services\UserRegisterServiceContract;
use App\Http\Requests\Register\ProducerRegisterRequest;
use App\Jobs\SendEmailVerificationJob;
use App\Models\Producer;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProducerRegisterService implements UserRegisterServiceContract
{
	/**
	 * @param ProducerRegisterRequest $request
	 * @return JsonResponse
	 */
	/** TODO - попробовать вынести валидацию реквеста выше - на контроллер, на контроллере уже ясно какой реквест */
	public function run($request)
	{
		DB::beginTransaction();
		try {
			$validated = collect($request->validated());

			/** @var Producer $producer */
			if (!$validated->get('producer'))
				$producer = Producer::create([
						'title' => $validated->get('new_producer_title')
					]);
			else
				$producer = Producer::find($validated->get('producer')['value']);

			/** @var User $user */
			$user = User::create([
				'name' => $validated->get('name'),
				'email' => $validated->get('email'),
				'password' => $validated->get('password'),
				'role_id' => $validated->get('role_id'),
				'producer_id' => $producer->id,
			]);

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
		return response()->json([$user, $producer])
			->withCookie(cookie('token', $token, 0, null, null, true, true, false, 'none'));
	}
}
