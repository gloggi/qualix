<?php

namespace App\Providers;

use App\Services\Translator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        app()->extend('translator', function($laravelTranslator) {
            return new Translator($laravelTranslator);
        });
    }
}
