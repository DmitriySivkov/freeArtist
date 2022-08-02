<?php

namespace App\Http\Controllers;

use App\Models\Producer;
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
			$relationRequest = $user->outgoingCoworkingRequests()
				->where('to_id', $request->get('producer'))
				->where('to_type', Producer::class)
				->first();

			if ($relationRequest) {
				switch ($relationRequest->status['id']) {
					case RelationRequest::STATUS_PENDING['id']:
						throw new \LogicException('Запрос обрабатывается изготовителем');
					case RelationRequest::STATUS_ACCEPTED['id']:
						throw new \LogicException('Вы уже состоите в команде этого изготовителя');
				}
			}

			$joinRequest = RelationRequest::create([
				'from_id' => $user->id,
				'from_type' => User::class,
				'to_id' => $request->get('producer'),
				'to_type' => Producer::class,
				'status' => RelationRequest::STATUS_PENDING['id'],
				'message' => $request->get('message')
			]);
		} catch (\Throwable $e) {
			return response()->json(["errors" =>
				["joinProducerRequest" => [$e->getMessage()]]
			])
				->setStatusCode(422);
		}

		return response()->json($joinRequest->load(['from', 'to']));
	}

	/**
	 * @param RelationRequest $relationRequest
	 * @return RelationRequest
	 */
	public function cancelCoworkingRequest(RelationRequest $relationRequest)
	{
		$relationRequest->status = RelationRequest::STATUS_REJECTED_BY_CONTRIBUTOR['id'];
		$relationRequest->save();
		return $relationRequest;
	}

	/**
	 * @param RelationRequest $relationRequest
	 * @return RelationRequest
	 */
	public function restoreCoworkingRequest(RelationRequest $relationRequest)
	{
		$relationRequest->status = RelationRequest::STATUS_PENDING['id'];
		$relationRequest->save();
		return $relationRequest;
	}

	/**
	 * @param Request $request
	 * @return User[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
	 */
	public function getNonOwnProducers(Request $request)
	{
		return User::nonOwnProducers()
			->when($request->has('query'), function($query) use ($request) {
				return $query->where('display_name', 'ilike', '%' . $request->get('query') . '%');
			})
			->limit(25)
			->orderBy('display_name', 'asc')
			->get();
	}
}
