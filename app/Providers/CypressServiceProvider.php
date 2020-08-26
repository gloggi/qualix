<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use NoelDeMartin\LaravelCypress\Facades\Cypress;

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

        Cypress::setModelsNamespace('App\Models');

        Route::namespace('App\Http\Controllers')
            ->middleware('web')
            ->group(function () {
                Route::get('/_cypress/create_snapshot/{name?}', 'CypressController@createSnapshot');
                Route::get('/_cypress/restore_snapshot/{name?}', 'CypressController@restoreSnapshot');
                Route::get('/_cypress/cleanup_snapshots', 'CypressController@cleanupSnapshots');
            });
    }
}
