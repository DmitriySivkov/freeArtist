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

Broadcast::channel('teams.{teamId}', function (\App\Models\User $user, $teamId) {
	$team = \App\Models\Team::find($teamId);
	return $team->users->pluck('id')->contains($user->id);
});

Broadcast::channel('permissions.{teamId}', function (\App\Models\User $user, $teamId) {
	$team = \App\Models\Team::find($teamId);
	return $team->users->pluck('id')->contains($user->id);
});

Broadcast::channel('relation-requests.user.{requestUserId}', function (\App\Models\User $user, $requestUserId) {
	return $user->id === (int)$requestUserId;
});

Broadcast::channel('producers.{producerId}.orders', function (\App\Models\User $user, $producerId) {
	$producer = \App\Models\Producer::find($producerId);

	return $producer->team->users->pluck('id')->contains($user->id);
});
