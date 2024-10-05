<?php

namespace App\Console\Commands;

use App\Http\Resources\UserTeamResource;
use App\Models\Permission;
use App\Models\Producer;
use App\Models\Team;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TestCommand extends Command
{
    protected $signature = 'test:go';

    public function handle()
    {
		$user = User::find(56);

		$userPermissionsByTeam = $user->permissions()
			->select(['id', 'name'])
			->get()
			->groupBy('pivot.team_id')
			->toArray();

		$test = $user->teams()
			->withPivot('role_id')
			->with([
				'detailed' => function(MorphTo $morphTo) {
					$morphTo->morphWith([
						Producer::class => ['logo'],
					]);
				}
			])
			->get()
			->map(function(Team $team) use ($userPermissionsByTeam) {
				$team->permissions = \Arr::exists($userPermissionsByTeam, $team->id) ?
					$userPermissionsByTeam[$team->id] :
					[];

				return $team;
			});

		info(print_r(
			UserTeamResource::collection($test)->collection,
			true
		));
    }
}
