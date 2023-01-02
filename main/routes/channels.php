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

/** user's requests */
Broadcast::channel('relation-requests.user.{userId}', function (\App\Models\User $user, $userId) {
	return $user->id === (int)$userId;
});

/** producer's requests */
Broadcast::channel('relation-requests.incoming.producer.{producerId}', function (\App\Models\User $user, $producerId) {
	$producer = \App\Models\Producer::find($producerId);
	return $user->id === $producer->team->user_id ||
		$user->hasPermission([
			\App\Models\Permission::PERMISSION_PRODUCER_INCOMING_COWORKING_REQUESTS['name']
		], $producer->team->name);
});

Broadcast::channel('permissions.{userId}', function (\App\Models\User $user, $userId) {
	return $user->id === (int)$userId;
});
