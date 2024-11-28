<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\TokenValidationController;
use App\Http\Controllers\Api\Admin\CompanyController;
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
use App\Http\Controllers\Api\Admin\ChatController;
use App\Http\Controllers\Api\Admin\TemplateController;
use App\Http\Controllers\Api\Admin\TemplateListController;
use App\Http\Controllers\Api\Admin\TemplateTaskController;
use App\Http\Controllers\Api\Admin\ProjectServiceController;
use App\Http\Controllers\Api\Admin\NotificationController;
use App\Http\Controllers\Api\Admin\SettingController;
use App\Http\Controllers\Api\Admin\ActivityLogController;

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

Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);


Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('verify-2fa-code', [AuthController::class, 'verify2FA']);

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

    Route::prefix('super-admin')->group(function () {
        Route::prefix('companies')->group(function () {
            Route::get('/', [CompanyController::class, 'index']);
            Route::post('/', [CompanyController::class, 'store']);
            Route::prefix('{uuid}')->group(function () {
                Route::get('/', [CompanyController::class, 'show']);
                Route::patch('/', [CompanyController::class, 'update']);
                Route::delete('/', [CompanyController::class, 'delete']);
                Route::post('/save-details', [CompanyController::class, 'saveDetails']);
                Route::post('/save-colors', [CompanyController::class, 'saveColors']);
                Route::patch('/update-active-state', [CompanyController::class, 'updateActiveState']);
                Route::delete('/delete-asset/{fileUuid}', [CompanyController::class, 'deleteAsset']);
                Route::prefix('users')->group(function () {
                    Route::get('/', [CompanyController::class, 'getAllUsers']);
                    Route::post('/store', [CompanyController::class, 'storeUser']);
                    Route::prefix('{userUuid}')->group(function () {
                        Route::patch('/update', [CompanyController::class, 'updateUser']);
                        Route::patch('/delete', [CompanyController::class, 'deleteUser']);
                    });
                });
                Route::prefix('upload')->group(function () {
                    Route::post('/logo', [CompanyController::class, 'uploadLogo']);
                    Route::post('/favicon', [CompanyController::class, 'uploadFavicon']);
                });
            });
        });
    });

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
                Route::patch('/update-2fa', [UsersController::class, 'update2FA']);
            });
            Route::get('/role/{role}', [UsersController::class, 'getByRole']);
        });

        Route::prefix('projects')->group(function () {
            Route::get('/type/{projectTypeId}', [ProjectController::class, 'getByType']);

            Route::get('/', [ProjectController::class, 'index']);
            Route::post('/', [ProjectController::class, 'store']);
            Route::prefix('{uuid}')->group(function () {
                Route::get('/activities', [ActivityLogController::class, 'index']);
                Route::get('/', [ProjectController::class, 'show']);
                Route::get('/users', [ProjectController::class, 'users']);
                Route::get('/users/all', [ProjectController::class, 'allUsers']);
                Route::patch('/users', [ProjectController::class, 'updateUsers']);
                Route::patch('/', [ProjectController::class, 'update']);
                Route::patch('/complete', [ProjectController::class, 'projectCompleted']);
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
                Route::patch('lists/sort', [ProjectListController::class, 'sortLists']);
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

                Route::prefix('templates')->group(function () {
                    Route::post('/', [TemplateController::class, 'store']);
                });

                Route::prefix('chat')->group(function () {
                    Route::get('/chats-and-contacts', [ChatController::class, 'chatsContacts']);
                    Route::get('/{userUuid}', [ChatController::class, 'show']);
                    Route::post('/{userUuid}', [ChatController::class, 'sendMessage']);
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

        Route::prefix('templates')->group(function () {
            Route::get('/', [TemplateController::class, 'index']);
            Route::get('/pagination', [TemplateController::class, 'templatesWithPagination']);

            Route::prefix('{templateUuid}')->group(function () {
                Route::post('/list', [TemplateListController::class, 'store']);
                Route::patch('/lists/sort', [TemplateListController::class, 'sortLists']);
                Route::get('/', [TemplateController::class, 'show']);
                Route::delete('/', [TemplateController::class, 'delete']);
            });

            Route::prefix('list')->group(function () {
                Route::prefix('{listUuid}')->group(function () {
                    Route::post('/task', [TemplateTaskController::class, 'store']);
                    Route::patch('/', [TemplateListController::class, 'update']);
                    Route::delete('/', [TemplateListController::class, 'delete']);
                });
            });

            Route::prefix('task')->group(function () {
                Route::prefix('{taskUuid}')->group(function () {
                    Route::patch('/', [TemplateTaskController::class, 'update']);
                    Route::delete('/', [TemplateTaskController::class, 'delete']);
                });
            });
        });

        Route::prefix('services')->group(function () {
            Route::get('/', [ProjectServiceController::class, 'index']);
            Route::get('/without-pagination', [ProjectServiceController::class, 'servicesWithoutPaginaton']);
            Route::patch('/sort', [ProjectServiceController::class, 'sortServices']);
            Route::post('/', [ProjectServiceController::class, 'store']);

            Route::prefix('type/{projectTypeUuid}')->group(function () {
                Route::get('/', [ProjectServiceController::class, 'getByType']);
            });
            Route::prefix('{serviceUuid}')->group(function () {
                Route::get('/', [ProjectServiceController::class, 'show']);
                Route::post('/', [ProjectServiceController::class, 'update']);
                Route::delete('/', [ProjectServiceController::class, 'delete']);
            });
        });

        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::post('/mark-as-read', [NotificationController::class, 'markAsRead']);
            Route::post('/mark-as-unread', [NotificationController::class, 'markAsUnRead']);
            Route::delete('/{uuid}', [NotificationController::class, 'delete']);
        });

        Route::prefix('settings')->group(function () {
            Route::get('/notifications', [SettingController::class, 'getNotificationSettings']);
            Route::patch('/notifications/{id}/update', [SettingController::class, 'updateNotificationSetting']);
        });

        Route::prefix('chat')->group(function () {
            Route::prefix('{uuid}')->group(function () {
                Route::post('/update-message', [ChatController::class, 'updateMessage']);
                Route::post('/delete-message', [ChatController::class, 'deleteMessage']);
            });
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
