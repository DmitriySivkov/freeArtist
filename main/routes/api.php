<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Personal\PersonalPaymentMethodController;
use App\Http\Controllers\Personal\PersonalTagController;
use App\Http\Controllers\ProducerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamRelationRequestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRelationRequestController;
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

Route::group(['prefix' => 'auth'], function() {
    Route::post('', [AuthController::class, 'login']);

	Route::post('viaToken', [AuthController::class, 'viaToken'])
		->middleware(\App\Http\Middleware\AppendAuthHeader::class);
});

Route::group(['prefix' => 'roles'], function() {
  Route::get('', [RoleController::class, 'index']);
});

Route::group(['prefix' => 'permissions'], function() {
	Route::get('', [PermissionController::class, 'index']);
});

Route::group(['prefix' => 'producers'], function() {
	Route::get('', [ProducerController::class, 'index']);

	Route::get('{producer}/products', [ProductController::class, 'index']);
	Route::get('{producer}/products/{product}', [ProductController::class, 'show']);
});

Route::group(['prefix' => 'cities'], function() {
	Route::get('', [CityController::class, 'index']);
});

Route::group(['prefix' => 'user'], function() {
	Route::get('location', [UserController::class, 'getLocation']);
});

Route::group(['prefix' => 'tags'], function() {
	Route::get('', [\App\Http\Controllers\TagController::class, 'index']);
});

Route::group(['prefix' => 'cart'], function() {
	Route::post('load', [\App\Http\Controllers\CartController::class, 'load'])
		->middleware([\App\Http\Middleware\CheckProducts::class]);
});

// do append a middleware since routes below apply both for auth & unauth users
Route::group(['prefix' => 'orders', 'middleware' => [\App\Http\Middleware\AppendAuthHeader::class]], function () {
	Route::get('', [OrderController::class, 'index']);
	Route::post('', [OrderController::class, 'store']);
	Route::post('transaction', [OrderController::class, 'transaction'])->middleware([\App\Http\Middleware\CheckProducts::class]);
});

Route::group(['prefix' => 'yookassa'], function() {
	Route::match(['POST', 'GET'], 'status', [\App\Http\Controllers\Yookassa\YookassaController::class, 'status']);
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

	Route::group(['prefix' => 'teams'], function() {
		Route::group(['prefix' => '{team}'], function() {
			Route::put('', [TeamController::class, 'update']);

			Route::group(['prefix' => 'users'], function() {
				Route::get('', [TeamController::class, 'getUsers']);
				Route::post('{user}/permissions/sync', [TeamController::class, 'syncUserPermissions']);
			});
		});
	});

	Route::group(['prefix' => 'producers'], function() {
		Route::post('register', [ProducerController::class, 'register']);
		Route::post('{producer}/setLogo', [ProducerController::class, 'setProducerLogo']);

		Route::group(['prefix' => '{producer}/products'], function () {
			Route::get('', [ProducerController::class, 'getProducerProducts']);
			Route::get('{product}', [ProducerController::class, 'getProducerProduct']);
			Route::get('thumbnails', [ProducerController::class, 'getProducerProductThumbnails']);
		});

		Route::group(['prefix' => '{producer}/orders'], function () {
			Route::get('', [\App\Http\Controllers\ProducerOrderController::class, 'index']);
			Route::post('move', [\App\Http\Controllers\ProducerOrderController::class, 'move']);
		});

		Route::group(['prefix' => '{producer}/payment-providers'], function () {
			Route::get('', [\App\Http\Controllers\ProducerPaymentProviderController::class, 'index']);
			Route::post('', [\App\Http\Controllers\ProducerPaymentProviderController::class, 'store']);
		});
	});

	Route::group(['prefix' => 'paymentMethods'], function() {
		Route::get('{producer}', [PersonalPaymentMethodController::class, 'getPaymentMethods']);
		Route::post('{producer}', [PersonalPaymentMethodController::class, 'setPaymentMethods']);
	});

	Route::group(['prefix' => 'products'], function() {
		Route::post('', [ProductController::class, 'store']);
		Route::post('{product}', [ProductController::class, 'update']);
		Route::delete('{product}', [ProductController::class, 'delete']);
	});

	Route::group(['prefix' => 'users'], function() {
		Route::get('nonRelatedTeams', [UserController::class, 'getNonRelatedTeams']);
	});

	Route::group(['prefix' => 'tags'], function() {
		Route::get('', [PersonalTagController::class, 'index']);
	});

	Route::group(['prefix' => 'relationRequests'], function() {
		Route::group(['prefix' => 'users'], function() {
			Route::get('', [UserRelationRequestController::class, 'index']);
			Route::post('', [UserRelationRequestController::class, 'store']);
			Route::put('{relationRequest}', [UserRelationRequestController::class, 'update']);
			Route::delete('{relationRequest}', [UserRelationRequestController::class, 'delete']);
		});

		Route::group(['prefix' => 'teams'], function() {
			Route::get('{team}', [TeamRelationRequestController::class, 'index']);
			Route::post('{team}/accept/{relationRequest}', [TeamRelationRequestController::class, 'accept']);
			Route::post('{team}/reject/{relationRequest}', [TeamRelationRequestController::class, 'reject']);
		});
	});
});
