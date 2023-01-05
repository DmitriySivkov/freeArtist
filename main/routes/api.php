<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProducerController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

Broadcast::routes(['middleware' => [\App\Http\Middleware\AppendAuthHeader::class, 'auth:sanctum']]);

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
Route::post('register', [AuthController::class, 'register']);

Route::post('authViaToken', [AuthController::class, 'authViaToken'])
	->middleware(\App\Http\Middleware\AppendAuthHeader::class);

Route::group(['prefix' => 'auth'], function() {
    Route::post('', [AuthController::class, 'login']);
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
	'middleware' => [
		\App\Http\Middleware\AppendAuthHeader::class,
		'auth:sanctum'
	],
	'prefix' => 'personal'
], function() {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::group(['prefix' => 'orders'], function () {
    	Route::get('', [OrderController::class, 'index']);
    	Route::post('', [OrderController::class, 'store']);
    });

	Route::group(['prefix' => 'teams'], function() {
		Route::get('{team}/users', [TeamController::class, 'getUsers']);
		Route::post('{team}/permissions/{user}/sync', [TeamController::class, 'syncUserPermissions']);
	});

	Route::group(['prefix' => 'producers'], function() {
		Route::post('register', [ProducerController::class, 'register']);
		Route::post('{producer}/setLogo', [ProducerController::class, 'setProducerLogo']);

		Route::group(['prefix' => '{producer}/products'], function () {
			Route::get('', [ProducerController::class, 'getProducerProducts']);
			Route::post('', [ProducerController::class, 'storeProducerProduct']);
			Route::delete('{product}', [ProducerController::class, 'deleteProducerProduct']);
			Route::post('{product}/addImage', [ProducerController::class, 'addProducerProductImage']);
			Route::post('{product}/syncCommonSettings', [ProducerController::class, 'syncProducerProductCommonSettings']);
			Route::post('{product}/syncCompositionSettings', [ProducerController::class, 'syncProducerProductCompositionSettings']);
		});

		Route::group(['prefix' => 'relationRequests'], function() {
			Route::get('incoming/{producer}', [ProducerController::class, 'getIncomingRelationRequests']);
			Route::post('{producer}/sendProducerPartnershipRequest', [ProducerController::class, 'sendProducerPartnershipRequest']);
			Route::post('acceptCoworkingRequest/{relationRequest}', [ProducerController::class, 'acceptCoworkingRequest']);
			Route::post('rejectCoworkingRequest/{relationRequest}', [ProducerController::class, 'rejectCoworkingRequest']);
		});
	});

	Route::group(['prefix' => 'users'], function() {
		Route::get('nonRelatedTeams', [UserController::class, 'getNonRelatedTeams']);

		Route::group(['prefix' => 'relationRequests'], function() {
			Route::post('createOutgoingRequest', [UserController::class, 'createOutgoingRequest']);
			Route::post('cancelCoworkingRequest/{relationRequest}', [UserController::class, 'cancelCoworkingRequest']);
			Route::post('restoreCoworkingRequest/{relationRequest}', [UserController::class, 'restoreCoworkingRequest']);
		});
	});

});
