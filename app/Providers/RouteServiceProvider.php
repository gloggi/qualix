<?php

namespace App\Providers;

use App\Models\Course;
use App\Models\FeedbackData;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });

        Route::bind('course', function($id) {
            /** @var User $user */
            $user = Auth::user();
            return $user->courses()->findOrFail($id);
        });
        Route::bind('category', function($id, \Illuminate\Routing\Route $route) {
            /** @var Course $course */
            $course = $route->parameter('course');
            return $course->categories()->findOrFail($id);
        });
        Route::bind('requirement', function($id, \Illuminate\Routing\Route $route) {
            /** @var Course $course */
            $course = $route->parameter('course');
            return $course->requirements()->findOrFail($id);
        });
        Route::bind('block', function($id, \Illuminate\Routing\Route $route) {
            /** @var Course $course */
            $course = $route->parameter('course');
            return $course->blocks()->findOrFail($id);
        });
        Route::bind('user', function($id, \Illuminate\Routing\Route $route) {
            /** @var Course $course */
            $course = $route->parameter('course');
            return $course->users()->findOrFail($id);
        });
        Route::bind('participant', function($id, \Illuminate\Routing\Route $route) {
            /** @var Course $course */
            $course = $route->parameter('course');
            return $course->participants()->findOrFail($id);
        });
        Route::bind('observation', function($id, \Illuminate\Routing\Route $route) {
            /** @var Course $course */
            $course = $route->parameter('course');
            return $course->observations()->findOrFail($id);
        });
        Route::bind('participantGroup', function($id, \Illuminate\Routing\Route $route) {
            /** @var Course $course */
            $course = $route->parameter('course');
            return $course->participantGroups()->findOrFail($id);
        });
        Route::bind('observationAssignment', function($id, \Illuminate\Routing\Route $route) {
            /** @var Course $course */
            $course = $route->parameter('course');
            return $course->observationAssignments()->findOrFail($id);
        });
        Route::bind('feedback_data', function($id, \Illuminate\Routing\Route $route) {
            /** @var Course $course */
            $course = $route->parameter('course');
            return $course->feedback_datas()
                ->with('feedbacks')
                ->with('feedbacks.participant')
                ->with('feedbacks.requirements')
                ->with('feedbacks.users')
                ->findOrFail($id);
        });
        Route::bind('feedback', function($id, \Illuminate\Routing\Route $route) {
            /** @var Participant $participant */
            $participant = $route->parameter('participant');
            return $participant->feedbacks()->findOrFail($id);
        });
        Route::bind('requirement_status', function($id, \Illuminate\Routing\Route $route) {
            /** @var Course $course */
            $course = $route->parameter('course');
            return $course->requirement_statuses()->findOrFail($id);
        });

        parent::boot();
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
