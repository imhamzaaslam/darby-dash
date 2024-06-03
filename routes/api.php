<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\TokenValidationController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\UsersController;
use App\Http\Controllers\Api\Admin\ProjectTypeController;
use App\Http\Controllers\Api\Admin\ProjectController;
use App\Http\Controllers\Api\Admin\ProjectListController;
use App\Http\Controllers\Api\Admin\TaskController;
use App\Http\Controllers\Api\Admin\CalendarFilterController;
use App\Http\Controllers\Api\Admin\ProjectProgressController;

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
                Route::post('/avatar', [UsersController::class, 'updateImage']);
                Route::delete('/', [UsersController::class, 'delete']);
            });
            Route::get('/role/{role}', [UsersController::class, 'getByRole']);
        });

        Route::prefix('projects')->group(function () {
            Route::get('/type/{projectTypeId}', [ProjectController::class, 'getByType']);

            Route::get('/', [ProjectController::class, 'index']);
            Route::post('/', [ProjectController::class, 'store']);
            Route::prefix('{uuid}')->group(function () {
                Route::get('/', [ProjectController::class, 'show']);
                Route::get('/users', [ProjectController::class, 'users']);
                Route::patch('/users', [ProjectController::class, 'updateUsers']);
                Route::patch('/', [ProjectController::class, 'update']);
                Route::delete('/', [ProjectController::class, 'delete']);

                Route::get('/lists', [ProjectListController::class, 'index']);
                Route::post('/list', [ProjectListController::class, 'store']);
                Route::prefix('list/{listUuid}')->group(function () {
                    Route::patch('/', [ProjectListController::class, 'update']);
                    Route::delete('/', [ProjectListController::class, 'delete']);
                });

                Route::get('/tasks', [TaskController::class, 'fetchUnlistedTasks']);
                Route::get('/allTasks', [TaskController::class, 'getByProject']);
                Route::post('/task', [TaskController::class, 'storeByProject']);
                Route::prefix('task/{taskUuid}')->group(function () {
                    Route::patch('/', [TaskController::class, 'updateByProject']);
                    Route::post('/order', [TaskController::class, 'updateProjectTasksOrder']);
                    Route::delete('/', [TaskController::class, 'deleteByProject']);
                });
                Route::get('/progress', [ProjectProgressController::class, 'index']);
            });
        });

        Route::prefix('list/{listUuid}')->group(function () {
            Route::get('/tasks', [TaskController::class, 'index']);
            Route::post('/task', [TaskController::class, 'store']);
            Route::prefix('task/{taskUuid}')->group(function () {
                Route::patch('/', [TaskController::class, 'update']);
                Route::delete('/', [TaskController::class, 'delete']);
            });
        });

        Route::get('/calendar-filters', [CalendarFilterController::class, 'index']);


        // Route::prefix('project/{id}/tasks')->group(function () {
        //     Route::get('/', [TaskController::class, 'index']);
        //     Route::post('/', [TaskController::class, 'store']);
        //     Route::prefix('{taskUuid}')->group(function () {
        //         Route::get('/', [TaskController::class, 'show']);
        //         Route::patch('/', [TaskController::class, 'update']);
        //         Route::delete('/', [TaskController::class, 'delete']);
        //     });
        // });
    });

    Route::get('/project-types', [ProjectTypeController::class, 'index']);
});
