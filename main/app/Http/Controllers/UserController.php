<?php

namespace App\Http\Controllers;

use App\Contracts\UserServiceContract;
use App\Models\RelationRequest;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
	public function getLocation()
	{
		/** @var UserService $userService */
		$userService = app(UserServiceContract::class);
		return $userService->getUserLocationByIp();
	}

	// todo - possibly move relation requests related methods to separate controller
	public function getRelationRequests()
	{
		/** @var User $user */
		$user = auth()->user();

		$outgoingRequests = $user->outgoingRelationRequests()
			->with(['to.team'])
			->orderBy('created_at', 'desc')
			->get();

		$incomingRequests = $user->incomingRelationRequests()
			->with(['from.team'])
			->orderBy('created_at', 'desc')
			->get();

		return $outgoingRequests->merge($incomingRequests);
	}

	public function storeRelationRequest(Request $request)
	{
		$teamId = $request->input('team_id');

		try {
			$team = Team::findOrFail($teamId);

			/** @var User $user */
			$user = auth()->user();

			if ($user->hasRole(Role::PRODUCER, $team->id)) {
				throw new \LogicException('Вы уже состоите в этой команде');
			}

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

	public function updateRelationRequest(Request $request, RelationRequest $relationRequest)
	{
		// todo - make a validator

		/** @var User $user */
		$user = auth()->user();

		// todo check if auth user === relation request creator

		$relationRequest->update($request->all());

		return $relationRequest;
	}
}
