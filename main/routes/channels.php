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

/** common user listens to his outgoing requests */
Broadcast::channel('relation-requests.user.{userId}', function (\App\Models\User $user, $userId) {
	return $user->id === (int)$userId;
});

/** producer listens to his incoming requests */
Broadcast::channel('relation-requests.producer.{producerId}', function (\App\Models\User $user, $producerId) {
	$producerTeam = \App\Models\Team::find($producerId);
	return $user->id === $producerTeam->user_id ||
		$user->hasPermission([
			\App\Models\Permission::PERMISSION_PRODUCER_INCOMING_COWORKING_REQUESTS['name']
		], $producerTeam->name);
});
