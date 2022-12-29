<?php

namespace App\Http\Controllers;

use App\Models\Team;
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
}
