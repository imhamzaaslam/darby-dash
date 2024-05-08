<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\TokenValidationController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\UsersController;

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

Route::post('/v1/token/validate', TokenValidationController::class);

Route::get('test', function () {
    return response()->json(['message' => 'Hello World!']);
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    Route::group(['middleware' => 'auth:sanctum'], function() {
      Route::post('logout', [AuthController::class, 'logout']);
      Route::get('user', [AuthController::class, 'user']);
    });
});

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Broadcast::routes(['middleware' => ['auth:sanctum']]);

    Route::get('/projects', function (Request $request) {
        dd('working fine');
    });

    Route::get('/roles', [RoleController::class, 'index']);

    Route::prefix('admin')->group(function () {
        Route::prefix('users')->group(function () {
            Route::get('/', [UsersController::class, 'index']);
            Route::post('/', [UsersController::class, 'store']);
            Route::prefix('{uuid}')->group(function () {
                Route::get('/', [UsersController::class, 'show']);
                Route::patch('/', [UsersController::class, 'update']);
                Route::delete('/', [UsersController::class, 'delete']);
            });
        });
    });
});