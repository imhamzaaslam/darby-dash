<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\TokenValidationController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\UsersController;
use App\Http\Controllers\Api\Admin\ProjectTypeController;
use App\Http\Controllers\Api\Admin\ProjectController;
use App\Http\Controllers\Api\Admin\ProjectListController;
use App\Http\Controllers\Api\Admin\TaskController;
use App\Http\Controllers\Api\Admin\StatusController;
use App\Http\Controllers\Api\Admin\CalendarFilterController;
use App\Http\Controllers\Api\Admin\ProjectProgressController;
use App\Http\Controllers\Api\Admin\MileStoneController;
use App\Http\Controllers\Api\Admin\CalendarEventController;
use App\Http\Controllers\Api\Admin\FileController;
use App\Http\Controllers\Api\Admin\FolderController;
use App\Http\Controllers\Api\Admin\PaymentController;
use App\Http\Controllers\Api\Admin\ProjectBucksController;

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
    Route::get('/roles/{id}/permissions', [RoleController::class, 'getPermissions']);
    Route::patch('/roles/{id}/permissions', [RoleController::class, 'updatePermissions']);

    Route::prefix('admin')->group(function () {
        Route::prefix('users')->group(function () {
            Route::get('/', [UsersController::class, 'index']);
            Route::get('/all', [UsersController::class, 'getAllUsers']);
            Route::post('/', [UsersController::class, 'store']);
            Route::prefix('{uuid}')->group(function () {
                Route::get('/', [UsersController::class, 'show']);
                Route::patch('/', [UsersController::class, 'update']);
                Route::post('/avatar', [UsersController::class, 'updateImage']);
                Route::delete('/', [UsersController::class, 'delete']);
                Route::patch('/update-password', [UsersController::class, 'updatePassword']);
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
                Route::get('/users/all', [ProjectController::class, 'allUsers']);
                Route::patch('/users', [ProjectController::class, 'updateUsers']);
                Route::patch('/', [ProjectController::class, 'update']);
                Route::delete('/', [ProjectController::class, 'delete']);
                Route::delete('/user/{userUuid}', [ProjectController::class, 'deleteUser']);
                
                Route::prefix('bucks')->group(function () {
                    Route::get('/', [ProjectBucksController::class, 'index']);
                    Route::patch('/', [ProjectBucksController::class, 'update']);
                    Route::get('/tasks', [TaskController::class, 'fetchBucksTasks']);
                    Route::prefix('tasks/{taskId}')->group(function () {
                        Route::patch('/', [TaskController::class, 'updateBucksTask']);
                    });
                });

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
                    Route::get('/members', [TaskController::class, 'getMembersForTask']);
                });
                Route::get('/progress', [ProjectProgressController::class, 'index']);

                Route::get('/lists-without-milestone', [ProjectListController::class, 'getListWithoutMilestone']);
                Route::prefix('milestones')->group(function () {
                    Route::get('/', [MileStoneController::class, 'index']);
                    Route::post('/', [MileStoneController::class, 'store']);
                });

                Route::get('/calendar-events', [CalendarEventController::class, 'index']);
                Route::post('/calendar-event', [CalendarEventController::class, 'store']);
                Route::prefix('calendar-event/{calendarEventUuid}')->group(function () {
                    Route::patch('/', [CalendarEventController::class, 'update']);
                    Route::delete('/', [CalendarEventController::class, 'delete']);
                });
                Route::prefix('folders')->group(function () {
                    Route::get('/', [FolderController::class, 'index']);
                    Route::post('/', [FolderController::class, 'store']);
                });
                Route::prefix('files')->group(function () {
                    Route::get('/', [FileController::class, 'index']);
                    Route::post('/', [FileController::class, 'store']);
                });

                Route::prefix('payments')->group(function () {
                    Route::get('/', [PaymentController::class, 'index']);
                    Route::post('/', [PaymentController::class, 'store']);
                });
            });
        });
        Route::prefix('milestones')->group(function () {
            Route::patch('/{milestoneUuid}', [MileStoneController::class, 'update']);
            Route::delete('/{milestoneUuid}', [MileStoneController::class, 'delete']);
        });

        Route::prefix('list/{listUuid}')->group(function () {
            Route::get('/tasks', [TaskController::class, 'index']);
            Route::post('/task', [TaskController::class, 'store']);
            Route::prefix('task/{taskUuid}')->group(function () {
                Route::patch('/', [TaskController::class, 'update']);
                Route::delete('/', [TaskController::class, 'delete']);
            });
        });

        Route::prefix('task')->group(function () {
            Route::prefix('{taskUuid}')->group(function () {
                Route::patch('/', [TaskController::class, 'updateAttributes']);
                Route::get('/files', [TaskController::class, 'getFiles']);
                Route::post('/files', [TaskController::class, 'storeFiles']);
                Route::post('/assign', [TaskController::class, 'assign']);
                Route::post('/unassign', [TaskController::class, 'unassign']);
            });
        });

        Route::prefix('folders/{folderUuid}')->group(function () {
            Route::patch('/', [FolderController::class, 'update']);
            Route::delete('/', [FolderController::class, 'delete']);
            Route::get('/files', [FolderController::class, 'getFiles']);
            Route::post('/files', [FolderController::class, 'storeFiles']);
        });

        Route::prefix('files/{fileUuid}')->group(function () {
            Route::delete('/', [FileController::class, 'delete']);
        });

        Route::get('/calendar-filters', [CalendarFilterController::class, 'index']);
        Route::get('/statuses', [StatusController::class, 'index']);

        Route::prefix('payments/{paymentUuid}')->group(function () {
            Route::get('/', [PaymentController::class, 'show']);
            Route::patch('/', [PaymentController::class, 'update']);
            Route::delete('/', [PaymentController::class, 'delete']);
        });

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
