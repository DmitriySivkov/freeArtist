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
/** todo - make private */
Broadcast::channel('relation-request', function () {
	return true;
});

//Broadcast::channel('incoming-relation-requests.{toUserId}', function ($user, $toUserId) {
//    return (int) $user->id === (int) $toUserId;
//});
//
//Broadcast::channel('outgoing-relation-requests.{fromUserId}', function ($user, $fromUserId) {
//	return (int) $user->id === (int) $fromUserId;
//});
