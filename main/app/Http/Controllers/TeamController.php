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
						$query->select([
							'permissions.id',
							'permissions.name',
							'permissions.display_name',
							'permissions.description',
							'teams.id as team_id'
						])
						->where('permission_user.team_id', $team->id)
						->leftJoin('teams', 'permission_user.team_id', '=', 'teams.id');
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

	/**
	 * @param Team $team
	 * @param TeamService $teamService
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getIncomingRequests(Team $team, TeamService $teamService)
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
	public function acceptRequest(Team $team, RelationRequest $relationRequest, TeamService $teamService)
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
	public function rejectRequest(Team $team, RelationRequest $relationRequest, TeamService $teamService)
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
