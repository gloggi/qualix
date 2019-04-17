<?php

namespace App\Providers;

use App\Http\ViewComposers\CurrentKursViewComposer;
use Illuminate\Support\Facades\View;
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
        View::composer('*', CurrentKursViewComposer::class);

        setlocale(LC_ALL, __('de_CH.utf8'));
    }
}
