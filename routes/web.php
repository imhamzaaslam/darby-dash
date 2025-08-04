<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\TestController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded ee by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route to view logs using Laravel Log Viewer package
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

Route::get('last-change', function () {
    return '7/7/2025 07:02 PM';
});

Route::get('send-test-email', function (Request $request) {
    if(!$request->has('email')) {
        return 'Email address is required.';
    }
    try {
        Mail::raw('This is a test email.', function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('Test Email');
        });
        return 'Test email sent successfully!';
    } catch (\Exception $e) {
        return 'Error sending email: ' . $e->getMessage();
    }
});

Route::get('/log-channel', function () {
    return response()->json([
        'default_log_channel' => config('logging.default'),
    ]);
});

Route::get('/add-test-log', function () {
    Log::info('Test log entry from web route.');
    return 'Test log entry created.';
});

Route::get('update-deleted-user-mails', [TestController::class, 'testUpdateDeletedUserMails']);

Route::get('{any?}', function() {
    return view('application');
})->where('any', '.*');
