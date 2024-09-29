<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Services\TeamService;
use Illuminate\Http\Request;

class TeamController extends Controller
{
	public function index(Request $request)
	{
		/** @var User $user */
		$user = auth()->user();

		$text = $request->input('q');
		$excludePersonalTeams = $request->input('exclude_personal', false);

		return Team::orderBy('display_name')
			->when($text, fn($query) =>
				$query->where('display_name', 'like', $text . '%')
			)
			->when($excludePersonalTeams, fn($query) =>
				$query->whereNotIn('id', $user->rolesTeams->pluck('id'))
			)
			->limit(25)
			->get();
	}

	/**
	 * @param Team $team
	 * @param Request $request
	 * @return bool|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
	 */
	public function update(Team $team, Request $request)
	{
		// todo - make rollback on catch everywhere
		try {
			return $team->update($request->all());
		} catch (\Throwable) {
			return response([
				'team' => Team::find($team->id),
				'message' => 'Не удалось'
			], 422);
		}
	}

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
}
