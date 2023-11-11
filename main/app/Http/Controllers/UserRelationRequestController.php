<?php

namespace App\Http\Controllers;

use App\Models\RelationRequest;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class UserRelationRequestController extends Controller
{
	public function index()
	{
		/** @var User $user */
		$user = auth()->user();

		return $user->outgoingRelationRequests()
			->with(['to.team'])
			->orderBy('created_at', 'desc')
			->get();
	}

    public function store(Request $request)
	{
		$teamId = $request->input('team_id');

		$team = Team::findOrFail($teamId);

		/** @var User $user */
		$user = auth()->user();

		try {
			/** @var RelationRequest|null $relationRequest */
			$relationRequest = $user->outgoingRelationRequests()
				->where('to_id', $team->detailed_id)
				->where('to_type', $team->detailed_type)
				->first();

			if ($relationRequest) {
				switch ($relationRequest->status) {
					case RelationRequest::STATUS_PENDING:
						throw new \LogicException('Запрос обрабатывается');
					case RelationRequest::STATUS_ACCEPTED:
						throw new \LogicException('Запрос принят');
				}
			}

			$outgoingRequest = RelationRequest::create([
				'from_id' => $user->id,
				'from_type' => User::class,
				'to_id' => $team->detailed_id,
				'to_type' => $team->detailed_type,
				'status' => RelationRequest::STATUS_PENDING,
				'message' => $request->get('message')
			]);

		} catch (\Throwable $e) {
			return response([
				'message' => $e->getMessage()
			], 422);
		}

		return $outgoingRequest->load(['to.team']);
	}

	public function update(Request $request, RelationRequest $relationRequest)
	{
		// todo - make a validator

		/** @var User $user */
		$user = auth()->user();

		// todo check if auth user === relation request creator

		$relationRequest->update($request->all());
	}

	public function delete(RelationRequest $relationRequest)
	{
		// todo - make a validator
		try {
			/** @var User $user */
			$user = auth()->user();

			// todo check if auth user === relation request creator

			if (
				in_array($relationRequest->status, [
					RelationRequest::STATUS_ACCEPTED,
					RelationRequest::STATUS_REJECTED_BY_RECIPIENT
				])
			) {
				throw new \LogicException('Запрос уже обработан');
			}

			return $relationRequest->delete();
		} catch (\Throwable $e) {
			return response([
				'message' => $e->getMessage()
			], 422);
		}
	}
}
