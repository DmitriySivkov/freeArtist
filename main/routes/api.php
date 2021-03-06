<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProducerController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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
Route::post('hasTokenCookie', [AuthController::class, 'hasTokenCookie']);

Route::group(['prefix' => 'auth'], function() {
    Route::post('', [AuthController::class, 'login']);
    Route::post('verify-email', [AuthController::class, 'verifyEmail']);
});

Route::group(['prefix' => 'roles'], function() {
  Route::get('', [RoleController::class, 'index']);
});

Route::group(['prefix' => 'producers'], function() {
	Route::get('', [ProducerController::class, 'index']);
	Route::get('{producer}', [ProducerController::class, 'show']);
});

/** auth requiring routes */
Route::group([
	'middleware' => [
		'appendAuthorizationHeader',
		'auth:sanctum'
	],
	'prefix' => 'personal'
], function() {
    Route::post('logout', [AuthController::class, 'logout']);
	Route::post('register', [RegisterController::class, 'store']);

    Route::group(['prefix' => 'orders'], function () {
    	Route::get('', [OrderController::class, 'index']);
    	Route::post('', [OrderController::class, 'store']);
    });

	Route::group(['prefix' => 'producers'], function() {
		Route::get('producersToAttach', [ProducerController::class, 'getProducersToAttach']);

		Route::group(['prefix' => 'relationRequests'], function() {
			Route::post('{producer}/sendProducerPartnershipRequest', [ProducerController::class, 'sendProducerPartnershipRequest']);
			Route::post('acceptCoworkingRequest/{relationRequest}', [ProducerController::class, 'acceptCoworkingRequest']);
			Route::post('rejectCoworkingRequest/{relationRequest}', [ProducerController::class, 'rejectCoworkingRequest']);
		});
	});

	Route::group(['prefix' => 'users'], function() {
		Route::group(['prefix' => 'relationRequests'], function() {
			Route::post('sendCoworkingRequest', [UserController::class, 'sendCoworkingRequest']);
			Route::post('cancelCoworkingRequest/{relationRequest}', [UserController::class, 'cancelCoworkingRequest']);
			Route::post('restoreCoworkingRequest/{relationRequest}', [UserController::class, 'restoreCoworkingRequest']);
		});
	});

});
