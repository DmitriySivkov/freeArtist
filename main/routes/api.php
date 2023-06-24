<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProducerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TagController;
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

Route::group(['prefix' => 'cities'], function() {
	Route::get('', [CityController::class, 'index']);
});

Route::group(['prefix' => 'user'], function() {
	Route::get('location', [UserController::class, 'getLocationByIp']);
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
		Route::group(['prefix' => '{team}'], function() {
			Route::put('', [TeamController::class, 'update']);

			Route::group(['prefix' => 'users'], function() {
				Route::get('', [TeamController::class, 'getUsers']);
				Route::post('{user}/permissions/sync', [TeamController::class, 'syncUserPermissions']);
			});

			Route::group(['prefix' => 'relationRequests'], function() {
				Route::get('incoming', [TeamController::class, 'getIncomingRequests']);
				Route::post('{relationRequest}/accept', [TeamController::class, 'acceptRequest']);
				Route::post('{relationRequest}/reject', [TeamController::class, 'rejectRequest']);
			});
		});
	});

	Route::group(['prefix' => 'producers'], function() {
		Route::post('register', [ProducerController::class, 'register']);
		Route::post('{producer}/setLogo', [ProducerController::class, 'setProducerLogo']);
		Route::post('{producer}/setStorefrontImage', [ProducerController::class, 'setProducerStorefrontImage']);

		Route::group(['prefix' => '{producer}/products'], function () {
			Route::get('', [ProducerController::class, 'getProducerProducts']);
			Route::get('{product}', [ProducerController::class, 'getProducerProduct']);
			Route::get('thumbnails', [ProducerController::class, 'getProducerProductThumbnails']);
		});
	});

	Route::group(['prefix' => 'products'], function() {
		Route::post('', [ProductController::class, 'store']);
		Route::post('{product}', [ProductController::class, 'update']);
		Route::delete('{product}', [ProductController::class, 'delete']);
	});

	Route::group(['prefix' => 'users'], function() {
		Route::get('nonRelatedTeams', [UserController::class, 'getNonRelatedTeams']);

		Route::group(['prefix' => 'relationRequests'], function() {
			Route::post('{team}/create', [UserController::class, 'createRequest']);
			Route::post('{relationRequest}/setStatus', [UserController::class, 'setRelationRequestStatus']);
		});
	});

	Route::group(['prefix' => 'tags'], function() {
		Route::get('', [TagController::class, 'index']);
	});

});
