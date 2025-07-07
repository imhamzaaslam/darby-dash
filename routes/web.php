<?php

use Illuminate\Support\Facades\Route;

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

Route::get('{any?}', function() {
    return view('application');
})->where('any', '.*');
