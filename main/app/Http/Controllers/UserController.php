<?php

namespace App\Http\Controllers;

use App\Models\RelationRequest;
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
			$producerUserRequest = Request::where('from', $user->id)
				->where('to', $request->get('producer'))
				->first();
			if ($producerUserRequest) {
				switch ($producerUserRequest->status) {
					case Request::STATUS_PENDING:
						throw new \LogicException('Запрос обрабатывается изготовителем');
					case Request::STATUS_ACCEPTED:
						throw new \LogicException('Вы уже являетесь партнёром этого изготовителя');
				}
			}

			$joinRequest = Request::create([
				'from' => $user->id,
				'to' => $request->get('producer'),
				'type' => Request::TYPE_COWORKING,
				'status' => Request::STATUS_PENDING,
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
