<?php

namespace App\Providers;

use App\Services\Validation\AllExistInCourse;
use App\Services\Validation\ExistsInCourse;
use App\Services\Validation\ValidFeedbackContent;
use App\Services\Validation\ValidFeedbackContentWithoutObservations;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register(): void {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        Validator::extend('existsInCourse', ExistsInCourse::class . '@validate');
        Validator::extend('allExistInCourse', AllExistInCourse::class . '@validate');
        Validator::extend('validFeedbackContent', ValidFeedbackContent::class . '@validate');
        Validator::extend('validFeedbackContentWithoutObservations', ValidFeedbackContentWithoutObservations::class . '@validate');
    }
}
