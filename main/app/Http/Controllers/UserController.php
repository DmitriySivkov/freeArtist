<?php

namespace App\Http\Controllers;

use App\Contracts\UserServiceContract;
use App\Models\Producer;
use App\Models\RelationRequest;
use App\Models\Team;
use App\Models\User;
use App\Services\ResponseService;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
	/**
	 * @param Team $team
	 * @param Request $request
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
	 */
	public function createRequest(Team $team, Request $request)
	{
		/** @var User $user */
		$user = auth()->user();

		try {
			$relationRequest = $user->outgoingRelationRequests()
				->where('to_id', $team->detailed_id)
				->where('to_type', $team->detailed_type)
				->first();

			if ($relationRequest) {
				switch ($relationRequest->status['id']) {
					case RelationRequest::STATUS_PENDING['id']:
						throw new \LogicException('Запрос обрабатывается компанией');
					case RelationRequest::STATUS_ACCEPTED['id']:
						throw new \LogicException('Вы уже состоите в этой компании');
				}
			}

			$outgoingRequest = RelationRequest::create([
				'from_id' => $user->id,
				'from_type' => User::class,
				'to_id' => $team->detailed_id,
				'to_type' => $team->detailed_type,
				'status' => RelationRequest::STATUS_PENDING['id'],
				'message' => $request->get('message')
			]);

		} catch (\Throwable $e) {
			return response([
				'message' => $e->getMessage()
			], 422);
		}

		return response()->json(
			$outgoingRequest->load(['from'])
				->loadMorph('to', [
					Producer::class => ['team']
				])
			);
	}

	/**
	 * @param RelationRequest $relationRequest
	 * @param Request $request
	 * @return RelationRequest
	 */
	public function setRelationRequestStatus(RelationRequest $relationRequest, Request $request)
	{
		/** @var User $user */
		$user = auth()->user();

		$relationRequest->status = collect(RelationRequest::STATUSES)
			->filter(fn($status) => $status['id'] === $request->get('status_id'))
			->first()['id'];

		$relationRequest->save();

		return $relationRequest;
	}

	/**
	 * @param Request $request
	 * @return User[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
	 */
	public function getNonRelatedTeams(Request $request)
	{
		/** @var User $user */
		$user = auth()->user();

		return $user->nonRelatedTeams()
			->when($request->has('query'), function($query) use ($request) {
				return $query->where('display_name', 'like', '%' . $request->get('query') . '%');
			})
			->limit(25)
			->orderBy('display_name')
			->get();
	}

	/**
	 * @return \App\Models\City[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
	 */
	public function getLocation()
	{
		/** @var UserService $userService */
		$userService = app(UserServiceContract::class);
		return $userService->getUserLocationByIp();
	}
}
