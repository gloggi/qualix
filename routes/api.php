<?php

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

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::post('_maintenance/clear-caches', function () {
    try {
        if (Artisan::call('storage:link') != 0) {
            abort(500, "Could not create storage link.\n");
        }
        return Artisan::output();
    } catch (ErrorException $e) {
        return "Could not create storage link, probably because of missing permissions.\n";
    }
});
Route::post('_maintenance/storage-link', function () {
    try {
        if (Artisan::call('storage:link') != 0) {
            abort(500, "Could not create storage link.\n");
        }
        return Artisan::output();
    } catch (ErrorException $e) {
        return "Could not create storage link, probably because of missing permissions.\n";
    }
});

Route::post('_maintenance/migrate', function () {
    if (Artisan::call('migrate', ['--force' => true]) != 0) {
        abort(500, "The migrations were not run successfully\n");
    }
    return Artisan::output();
});
