<?php

namespace App\Providers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
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

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }
}
