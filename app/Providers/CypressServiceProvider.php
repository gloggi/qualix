<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laracasts\Cypress\Controllers\CypressController;

class CypressServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('production')) {
            return;
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->environment('production')) {
            return;
        }

        $this->app->bind('CypressController', \App\Http\Controllers\CypressController::class);
        Route::middleware('web')
            ->group(function () {
                Route::get('/__cypress__/create-snapshot/{name?}', 'CypressController@createSnapshot');
                Route::get('/__cypress__/restore-snapshot/{name?}', 'CypressController@restoreSnapshot');
                Route::get('/__cypress__/cleanup-snapshots', 'CypressController@cleanupSnapshots');
                Route::post('/__cypress__/generate', 'CypressController@generate');
            });
    }
}
