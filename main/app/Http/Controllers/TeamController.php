<?php

namespace App\Http\Controllers;

use App\Models\RelationRequest;
use App\Models\Team;
use App\Models\User;
use App\Services\TeamService;
use Illuminate\Http\Request;

class TeamController extends Controller
{
	/**
	 * @param Team $team
	 * @return \App\Models\User[]|\Illuminate\Database\Eloquent\Collection
	 */
	public function getUsers(Team $team)
	{
		return $team->users->load([
				'permissions' => function($query) use ($team) {
					$query->where('team_id', $team->id);
				}
			]);
	}

	/**
	 * @param Request $request
	 * @param Team $team
	 * @param User $user
	 * @param TeamService $teamService
	 * @return \Illuminate\Http\JsonResponse|\Illuminate\Support\Collection
	 */
	public function syncUserPermissions(Request $request, Team $team, User $user, TeamService $teamService)
	{
		try {
			return $teamService->syncUserPermissions($request->all(), $team, $user);
		} catch (\Throwable $e) {
			return response()->json($e->getMessage())
				->setStatusCode(422);
		}
	}


	public function getIncomingRequests(Team $team, TeamService $teamService)
	{
		$teamService->setTeam($team);

		return response()->json(
			$teamService->getTeamIncomingRequests()
		);
	}

	public function acceptRequest(Team $team, RelationRequest $relationRequest, TeamService $teamService)
	{
		try {
			$teamService->setTeam($team);
			return $teamService->acceptRequest($relationRequest);
		} catch (\Throwable $e) {
			return response()->json($e->getMessage())
				->setStatusCode(422);
		}
	}

	public function rejectRequest(Team $team, RelationRequest $relationRequest, TeamService $teamService)
	{
		try {
			$teamService->setTeam($team);
			return $teamService->rejectRequest($relationRequest);
		} catch (\Throwable $e) {
			return response()->json($e->getMessage())
				->setStatusCode(422);
		}
	}
}
