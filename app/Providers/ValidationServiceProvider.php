<?php

namespace App\Providers;

use App\Services\Validation\AllExistInCourse;
use App\Services\Validation\ExistsInCourse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        Validator::extend('existsInCourse', ExistsInCourse::class . '@validate');
        Validator::extend('allExistInCourse', AllExistInCourse::class . '@validate');
    }
}
