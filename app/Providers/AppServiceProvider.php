<?php

namespace App\Providers;

use App\Services\Translator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->extend('translator', function($laravelTranslator) {
            return new Translator($laravelTranslator);
        });

        $this->app->singleton('csp-nonce', function () {
            return Str::random(32);
        });
    }
}
