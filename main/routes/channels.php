<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('relation-requests.user.{userId}', function (\App\Models\User $user, $userId) {
	return $user->id === (int)$userId;
});

Broadcast::channel('relation-requests.team.{teamId}', function (\App\Models\User $user, $teamId) {
	$team = \App\Models\Team::find($teamId);
	return $user->id === $team->user_id ||
		$user->hasPermission([
			\App\Models\Permission::PERMISSION_PRODUCER_REQUESTS['name'] // todo - add other types later
		], $team->name);
});

Broadcast::channel('permissions.{userId}', function (\App\Models\User $user, $userId) {
	return $user->id === (int)$userId;
});
