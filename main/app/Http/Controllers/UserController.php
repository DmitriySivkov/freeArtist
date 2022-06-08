<?php

namespace App\Http\Controllers;

use App\Models\ProducerUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
	/**
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function joinProducer(Request $request)
	{
		/** @var User $user */
		$user = auth('sanctum')->user();
		try {
			$producerUserRequest = ProducerUserRequest::where('from', $user->id)
				->where('to', $request->get('producer'))
				->first();
			if ($producerUserRequest) {
				switch ($producerUserRequest->status) {
					case ProducerUserRequest::STATUS_PENDING:
						throw new \LogicException('Запрос обрабатывается изготовителем');
					case ProducerUserRequest::STATUS_ACCEPTED:
						throw new \LogicException('Вы уже являетесь партнёром этого изготовителя');
				}
			}

			$joinRequest = ProducerUserRequest::create([
				'from' => $user->id,
				'to' => $request->get('producer'),
				'type' => ProducerUserRequest::TYPE_USER_TO_PRODUCER,
				'status' => ProducerUserRequest::STATUS_PENDING,
				'message' => $request->get('message')
			]);
		} catch (\Throwable $e) {
			return response()->json(["errors" =>
				["joinProducerRequest" => [$e->getMessage()]]
			])
				->setStatusCode(422);
		}

		return response()->json($joinRequest);
	}
}
