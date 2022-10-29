<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProducerController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

/** won't work in broadcastServiceProvider for some reason */
Broadcast::routes(['middleware' => ['auth:sanctum']]);

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/** no auth requiring routes */
Route::post('register', [RegisterController::class, 'store']);
Route::post('authViaSession', [AuthController::class, 'authViaSession']);

// todo - incognito mode does not support session cookie
Route::group(['prefix' => 'auth'], function() {
    Route::post('', [AuthController::class, 'login']);
    Route::post('verify-email', [AuthController::class, 'verifyEmail']);
});

Route::group(['prefix' => 'roles'], function() {
  Route::get('', [RoleController::class, 'index']);
});

Route::group(['prefix' => 'permissions'], function() {
	Route::get('', [PermissionController::class, 'index']);
});

Route::group(['prefix' => 'producers'], function() {
	Route::get('', [ProducerController::class, 'index']);
	Route::get('{producer}', [ProducerController::class, 'show']);
});

/** auth requiring routes */
Route::group([
	'middleware' => ['auth:sanctum'],
	'prefix' => 'personal'
], function() {
    Route::post('logout', [AuthController::class, 'logout']);
	Route::post('register', [RegisterController::class, 'store']);

    Route::group(['prefix' => 'orders'], function () {
    	Route::get('', [OrderController::class, 'index']);
    	Route::post('', [OrderController::class, 'store']);
    });

	Route::group(['prefix' => 'producers'], function() {
		Route::get('{producer}/users', [ProducerController::class, 'getProducerUsers']);

		Route::group(['prefix' => '{producer}/products'], function () {
			Route::get('', [ProducerController::class, 'getProducerProducts']);
			Route::post('', [ProducerController::class, 'storeProducerProduct']);
			Route::delete('{product}', [ProducerController::class, 'deleteProducerProduct']);
			Route::post('{product}/addImage', [ProducerController::class, 'addProducerProductImage']);
			Route::post('{product}/syncCommonSettings', [ProducerController::class, 'syncProducerProductCommonSettings']);
			Route::post('{product}/syncCompositionSettings', [ProducerController::class, 'syncProducerProductCompositionSettings']);
		});

		Route::group(['prefix' => 'permissions'], function() {
			Route::post('{producer}/sync/{user}', [ProducerController::class, 'syncUserPermissions']);
		});
		Route::group(['prefix' => 'relationRequests'], function() {
			Route::get('incoming/{producer}', [ProducerController::class, 'getIncomingRelationRequests']);
			Route::post('{producer}/sendProducerPartnershipRequest', [ProducerController::class, 'sendProducerPartnershipRequest']);
			Route::post('acceptCoworkingRequest/{relationRequest}', [ProducerController::class, 'acceptCoworkingRequest']);
			Route::post('rejectCoworkingRequest/{relationRequest}', [ProducerController::class, 'rejectCoworkingRequest']);
		});
	});

	Route::group(['prefix' => 'users'], function() {
		Route::get('nonRelatedProducers', [UserController::class, 'getNonRelatedProducers']);

		Route::group(['prefix' => 'relationRequests'], function() {
			Route::post('sendCoworkingRequest', [UserController::class, 'sendCoworkingRequest']);
			Route::post('cancelCoworkingRequest/{relationRequest}', [UserController::class, 'cancelCoworkingRequest']);
			Route::post('restoreCoworkingRequest/{relationRequest}', [UserController::class, 'restoreCoworkingRequest']);
		});
	});

});
