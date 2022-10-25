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

/** sender-user and receiver-producer know about related requests state */
Broadcast::channel('relation-requests.user.{fromId}.producer.{toId}', function (\App\Models\User $user, $fromId, $toId) {
	$producerTeam = \App\Models\Team::find($toId);
	return $user->id === $fromId ||
		(
			$user->id === $producerTeam->user_id ||
			$user->hasPermission([
				\App\Models\Permission::PERMISSION_PRODUCER_INCOMING_COWORKING_REQUESTS['name']
			], $producerTeam->name)
		);
});
