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
	 * @return array|\Illuminate\Database\Eloquent\Collection
	 */
	public function index(Team $team, TeamService $teamService)
	{
		$teamService->setTeam($team);

		return $teamService->getTeamIncomingRequests();
	}

	/**
	 * @param Team $team
	 * @param RelationRequest $relationRequest
	 * @param TeamService $teamService
	 * @return RelationRequest|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
	 * @throws \Throwable
	 */
	public function accept(Team $team, RelationRequest $relationRequest, TeamService $teamService)
	{
		try {
			\DB::beginTransaction();

			$teamService->setTeam($team);
			$request = $teamService->acceptRequest($relationRequest);

			\DB::commit();

			return $request;
		} catch (\Throwable $e) {
			\DB::rollBack();

			return response([
				'message' => $e->getMessage()
			], 422);
		}
	}

	/**
	 * @param Team $team
	 * @param RelationRequest $relationRequest
	 * @param TeamService $teamService
	 * @return RelationRequest|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
	 * @throws \Throwable
	 */
	public function reject(Team $team, RelationRequest $relationRequest, TeamService $teamService)
	{
		try {
			\DB::beginTransaction();

			$teamService->setTeam($team);
			$request = $teamService->rejectRequest($relationRequest);

			\DB::commit();

			return $request;
		} catch (\Throwable $e) {
			\DB::rollBack();

			return response([
				'message' => $e->getMessage()
			], 422);
		}
	}
}
