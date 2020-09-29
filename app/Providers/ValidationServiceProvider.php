<?php

namespace App\Providers;

use App\Services\Validation\AllExistInCourse;
use App\Services\Validation\ExistsInCourse;
use App\Services\Validation\ValidQualiContent;
use App\Services\Validation\ValidQualiContentWithoutObservations;
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
        Validator::extend('validQualiContent', ValidQualiContent::class . '@validate');
        Validator::extend('validQualiContentWithoutObservations', ValidQualiContentWithoutObservations::class . '@validate');
    }
}
