<?php

namespace App\Http\Controllers;

use App\Models\RelationRequest;
use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Http\Request;

class TeamRelationRequestController extends Controller
{
	/**
	 * @param Team $team
	 * @param TeamService $teamService
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function index(Team $team, TeamService $teamService)
	{
		$teamService->setTeam($team);

		return response()->json(
			$teamService->getTeamIncomingRequests()
		);
	}

	/**
	 * @param Team $team
	 * @param RelationRequest $relationRequest
	 * @param TeamService $teamService
	 * @return RelationRequest|\Illuminate\Http\JsonResponse
	 * @throws \Throwable
	 */
	public function accept(Team $team, RelationRequest $relationRequest, TeamService $teamService)
	{
		\DB::beginTransaction();
		try {
			$teamService->setTeam($team);
			$request = $teamService->acceptRequest($relationRequest);

			\DB::commit();
			return $request;
		} catch (\Throwable $e) {
			\DB::rollBack();
			return response()->json($e->getMessage())
				->setStatusCode(422);
		}
	}

	/**
	 * @param Team $team
	 * @param RelationRequest $relationRequest
	 * @param TeamService $teamService
	 * @return RelationRequest|\Illuminate\Http\JsonResponse
	 * @throws \Throwable
	 */
	public function reject(Team $team, RelationRequest $relationRequest, TeamService $teamService)
	{
		\DB::beginTransaction();
		try {
			$teamService->setTeam($team);
			$request = $teamService->rejectRequest($relationRequest);

			\DB::commit();
			return $request;
		} catch (\Throwable $e) {
			\DB::rollBack();
			return response()->json($e->getMessage())
				->setStatusCode(422);
		}
	}
}
