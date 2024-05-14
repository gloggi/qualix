<?php

namespace App\Providers;

use App\Auth\HitobitoProvider;
use App\Http\ViewComposers\CurrentCourseViewComposer;
use App\Models\Course;
use App\Models\Participant;
use App\Models\User;
use App\Observers\DeleteOrphanObservationsOnParticipantDelete;
use App\Services\Hashing\NullableBcryptHasher;
use App\Services\Translator;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
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

        $this->app->make('hash')->extend('bcrypt', function () {
            return new NullableBcryptHasher(config('hashing.bcrypt') ?? []);
        });

        $this->app->singleton('csp-nonce', function () {
            return Str::random(32);
        });

        Route::bind('course', function($id) {
            /** @var User $user */
            $user = Auth::user();
            return $user->courses()->findOrFail($id);
        });

        Route::bind('requirement', function($id, \Illuminate\Routing\Route $route) {
            /** @var Course $course */
            $course = $route->parameter('course');
            return $course->requirements()->findOrFail($id);
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

        Participant::observe(DeleteOrphanObservationsOnParticipantDelete::class);

        View::composer('*', CurrentCourseViewComposer::class);

        $this->bootHitobitoSocialite();
    }

    private function bootHitobitoSocialite()
    {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $socialite->extend(
            'hitobito',
            function ($app) {
                $config = config('services.hitobito');
                return new HitobitoProvider(
                    $this->app['request'], $config['base_url'], $config['client_id'],
                    $config['client_secret'], $this->formatRedirectUrl($config),
                    Arr::get($config, 'guzzle', [])
                );
            }
        );
    }

    /**
     * Format the callback URL, resolving a relative URI if needed.
     *
     * @param  array  $config
     * @return string
     */
    protected function formatRedirectUrl(array $config)
    {
        $redirect = value($config['redirect']);

        return Str::startsWith($redirect, '/')
            ? $this->app['url']->to($redirect)
            : $redirect;
    }
}
