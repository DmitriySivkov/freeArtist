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
	public function sendCoworkingRequest(Request $request)
	{
		/** @var User $user */
		$user = auth('sanctum')->user();
		try {
			$relationRequest = RelationRequest::where('from', $user->id)
				->where('to', $request->get('producer'))
				->first();

			if ($relationRequest) {
				switch ($relationRequest->status['id']) {
					case RelationRequest::STATUS_PENDING['id']:
						throw new \LogicException('Запрос обрабатывается изготовителем');
					case RelationRequest::STATUS_ACCEPTED['id']:
						throw new \LogicException('Вы уже являетесь партнёром этого изготовителя');
				}
			}

			$joinRequest = RelationRequest::create([
				'from' => $user->id,
				'to' => $request->get('producer'),
				'type' => RelationRequest::TYPE_COWORKING,
				'status' => RelationRequest::STATUS_PENDING['id'],
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

	public function cancelCoworkingRequest(RelationRequest $relationRequest)
	{
		$relationRequest->status = RelationRequest::STATUS_REJECTED_BY_CONTRIBUTOR['id'];
		$relationRequest->save();
		return $relationRequest;
	}

	public function restoreCoworkingRequest(RelationRequest $relationRequest)
	{
		$relationRequest->status = RelationRequest::STATUS_PENDING['id'];
		$relationRequest->save();
		return $relationRequest;
	}
}
