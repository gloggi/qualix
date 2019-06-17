<?php

namespace App\Providers;

use App\Http\ViewComposers\CurrentCourseViewComposer;
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
        View::composer('*', CurrentCourseViewComposer::class);

        setlocale(LC_ALL, __('de_CH.utf8'));
    }
}
