<?php

use App\Http\Controllers\Api\Admin\ActivityLogController;
use App\Http\Controllers\Api\Admin\ArticleCategoryController;
use App\Http\Controllers\Api\Admin\ArticlesController;
use App\Http\Controllers\Api\Admin\CategoriesController;
use App\Http\Controllers\Api\Admin\EntitiesController;
use App\Http\Controllers\Api\Admin\InvoiceController;
use App\Http\Controllers\Api\Admin\UsersController as AdminUsersController;
use App\Http\Controllers\Api\Admin\JournalsController as AdminJournalsController;
use App\Http\Controllers\Api\Admin\JournalEntriesController as AdminJournalEntriesController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CountriesController;
use App\Http\Controllers\Api\CredentialsController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PlatformsController;
use App\Http\Controllers\Api\ProductsController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\StatisticsController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\VatNumbersController;
use App\Http\Controllers\Api\TokenValidationController;
use Illuminate\Support\Facades\Broadcast;
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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('/v1/token/validate', TokenValidationController::class);

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Broadcast::routes(['middleware' => ['auth:sanctum']]);

    Route::get('/me', AuthController::class);
    Route::get('/countries', [CountriesController::class, 'index']);
    Route::get('/platforms', [PlatformsController::class, 'index']);

    Route::prefix('products')->group(function () {
        Route::get('/', [ProductsController::class, 'index']);
        Route::get('/{uuid}', [ProductsController::class, 'show']);
    });

    Route::middleware('admin')->group(function () {
        Route::prefix('invoices')->group(function () {
            Route::get('', [InvoiceController::class, 'index']);
            Route::get('{uuid}', [InvoiceController::class, 'show']);
        });

        Route::prefix('platforms')->group(function () {
            Route::post('', [PlatformsController::class, 'store']);
            Route::patch('{uuid}', [PlatformsController::class, 'update']);
        });

        Route::prefix('users')->group(function () {
            Route::prefix('{uuid}')->group(function () {
                Route::prefix('/platforms')->group(function () {
                    Route::get('', [PlatformsController::class, 'userPlatforms']);
                });
            });
        });

        Route::prefix('products')->group(function () {
            Route::patch('/{uuid}', [ProductsController::class, 'update']);
        });

        Route::prefix('admin')->group(function () {
            Route::get('/categories', [CategoriesController::class, 'index']);

            Route::prefix('users')->group(function () {
                Route::get('', [AdminUsersController::class, 'index']);
                Route::post('', [AdminUsersController::class, 'store']);

                Route::prefix('{uuid}')->group(function () {
                    Route::get('', [AdminUsersController::class, 'show']);
                    Route::patch('', [AdminUsersController::class, 'update']);
                });
            });

            Route::prefix('support')->group(function () {
                Route::post('/articles', [ArticlesController::class, 'store']);
                Route::patch('/articles/{article}', [ArticlesController::class, 'update']);
                Route::delete('/articles/{article}', [ArticlesController::class, 'delete']);

                Route::post('/categories', [ArticleCategoryController::class, 'store']);
            });

        Route::prefix('journals')->group(function () {
            Route::get('', [AdminJournalsController::class, 'index']);
            Route::get('/count', [AdminJournalsController::class, 'getJournalsCountsByStatus']);
            Route::post('', [AdminJournalsController::class, 'store']);
            Route::get('/{uuid}', [AdminJournalsController::class, 'getJournalsForUser']);
            Route::patch('/{uuid}', [AdminJournalsController::class, 'update']);

                Route::prefix('/{uuid}/entries')->group(function () {
                    Route::patch('', [AdminJournalEntriesController::class, 'updateEntries']);
                    Route::patch('{entry}', [AdminJournalEntriesController::class, 'update']);
                    Route::delete('{entry}', [AdminJournalEntriesController::class, 'delete']);
                });
            });

            Route::prefix('logs')->group(function () {
                Route::get('', [ActivityLogController::class, 'index']);
            });

            Route::prefix('entities')->group(function () {
                Route::get('', [EntitiesController::class, 'index']);
            });
        });
    });

    Route::get('/support/articles/{article}', [ArticlesController::class, 'show']);
    Route::get('/support/articles', [ArticlesController::class, 'index']);
    Route::get('/support/categories', [ArticleCategoryController::class, 'index']);
    Route::post('/users/{uuid}/files', [FileController::class, 'store']);
    Route::post('/platforms/{uuid}/files', [FileController::class, 'store']);
    Route::patch('/users/{uuid}/files/{fileUuid}', [FileController::class, 'update']);
    Route::patch('/platforms/{uuid}/files/{fileUuid}', [FileController::class, 'update']);

    Route::prefix('/users/{uuid}')->group(function () {
        Route::get('', [UsersController::class, 'show']);
        Route::patch('', [UsersController::class, 'update']);

        Route::prefix('notifications')->group(function () {
            Route::get('', [NotificationController::class, 'index']);
            Route::get('{id}', [NotificationController::class, 'show']);
            Route::patch('{id}', [NotificationController::class, 'update']);
        });

        Route::prefix('/products')->group(function () {
            Route::get('', [ProductsController::class, 'userProducts']);
        });

        Route::prefix('/shops')->group(function () {
            Route::get('', [ShopController::class, 'index']);
            Route::post('', [ShopController::class, 'store']);

            Route::prefix('{shopUuid}')->group(function () {
                Route::get('', [ShopController::class, 'show']);
                Route::patch('', [ShopController::class, 'update']);
                Route::delete('', [ShopController::class, 'delete']);

                Route::prefix('/credential')->group(function () {
                    Route::get('', [CredentialsController::class, 'show']);
                    Route::post('', [CredentialsController::class, 'store']);
                    Route::patch('', [CredentialsController::class, 'update']);
                    Route::delete('', [CredentialsController::class, 'delete']);
                });
            });
        });

        Route::prefix('/vat-numbers')->group(function () {
            Route::get('', [VatNumbersController::class, 'index']);
            Route::post('', [VatNumbersController::class, 'store']);
            Route::patch('/toggle-oss', [VatNumbersController::class, 'updateOss']); // deprecated.

            Route::prefix('{vatNumberUuid}')->group(function () {
                Route::get('', [VatNumbersController::class, 'show']);
                Route::patch('', [VatNumbersController::class, 'update']);
            });
        });

        Route::middleware('customer')->group(function () {
            Route::prefix('products')->group(function () {
                Route::post('import-products-csv', [ProductsController::class, 'importProductsFromCsv']);
                Route::get('download-sample-csv', [ProductsController::class, 'downloadSampleCsv']);
            });

            Route::prefix('sales')->group(function () {
                Route::get('statistics', [statisticsController::class, 'statistics']);
                Route::get('vat-statistics', [statisticsController::class, 'vatStatistics']);
                Route::post('import-products-csv', [ProductsController::class, 'importProductsFromCsv']);
            });
        });
    });
});
