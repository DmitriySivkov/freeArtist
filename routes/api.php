<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RoleController;
use Illuminate\Http\Request;
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
Route::post('register', [RegisterController::class, 'store']);
Route::post('auth', [AuthController::class, 'auth']);
Route::post('logout', [AuthController::class, 'logout']);

Route::group(['prefix' => 'roles'], function() {
  Route::get('', [RoleController::class, 'index']);
});

Route::middleware(['auth:api'])->group(function () {

});
