<?php

use App\Http\Controllers\E2ETestingController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::get('/__e2e__/csrf_token',                    [E2ETestingController::class, 'csrfToken']);
    Route::post('/__e2e__/login',                        [E2ETestingController::class, 'login']);
    Route::post('/__e2e__/logout',                       [E2ETestingController::class, 'logout']);
    Route::post('/__e2e__/create',                       [E2ETestingController::class, 'create']);
    Route::post('/__e2e__/generate',                     [E2ETestingController::class, 'generate']);
    Route::post('/__e2e__/artisan',                      [E2ETestingController::class, 'artisan']);
    Route::post('/__e2e__/run-php',                      [E2ETestingController::class, 'runPhp']);
    Route::get('/__e2e__/create-snapshot/{name?}',       [E2ETestingController::class, 'createSnapshot']);
    Route::get('/__e2e__/restore-snapshot/{name?}',      [E2ETestingController::class, 'restoreSnapshot']);
    Route::get('/__e2e__/cleanup-snapshots',             [E2ETestingController::class, 'cleanupSnapshots']);
});
