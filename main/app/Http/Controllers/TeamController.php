<?php

namespace App\Http\Controllers;

use App\Http\Resources\TeamUsersPermissionsResource;
use App\Models\Team;
use App\Models\User;
use App\Services\TeamService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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
	 * @return \Illuminate\Support\Collection
	 */
	public function getUsersPermissions(Team $team)
	{
		return TeamUsersPermissionsResource::collection(
			$team->users->load([
				'permissions' => function($query) use ($team) {
					$query->select([
						'permissions.name',
					])
						->where('permission_user.team_id', $team->id)
						->leftJoin('teams', 'permission_user.team_id', '=', 'teams.id');
				}
			])
		)->collection;
	}

	/**
	 * @param Request $request
	 * @param Team $team
	 * @param User $user
	 * @param TeamService $teamService
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|void
	 */
	public function updateUserPermissions(Request $request, Team $team, User $user, TeamService $teamService)
	{
		$permissions = $request->input('permissions', []);

		// todo - validation
		try {
			$teamService->syncUserPermissions($permissions, $team, $user);
		} catch (\Throwable $e) {
			\Log::error(
				"Не удалось обновить права пользователя, team_id:{$team->id}, user_id:{$user->id}" . PHP_EOL .
				'текст ошибки: ' . PHP_EOL .
				$e->getMessage()
			);

			return response(null, Response::HTTP_UNPROCESSABLE_ENTITY);
		}
	}
}
