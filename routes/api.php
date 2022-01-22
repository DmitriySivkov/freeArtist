<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RoleController;
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
Route::group(['prefix' => 'auth'], function() {
    Route::post('', [AuthController::class, 'login']);
    Route::post('verify-email', [AuthController::class, 'verifyEmail']);
});

Route::post('register', [RegisterController::class, 'store']);
Route::post('hasTokenCookie', [AuthController::class, 'hasTokenCookie']);

Route::group(['prefix' => 'roles'], function() {
  Route::get('', [RoleController::class, 'index']);
});

Route::group(['middleware' => ['appendAuthorizationHeader', 'auth:api']], function() {
    Route::post('logout', [AuthController::class, 'logout']);
});
