<?php

use App\Http\Middleware\CourseMustNotBeArchived;
use App\Http\Middleware\Localization;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\RestoreFormDataFromExpiredSession;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\TrimStrings;
use App\Http\Middleware\UpdateLeiterLastAccessed;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->replace(
            \Illuminate\Foundation\Http\Middleware\TrimStrings::class, TrimStrings::class
        );
        $middleware->replace(
            \Illuminate\Auth\Middleware\RedirectIfAuthenticated::class, RedirectIfAuthenticated::class
        );
        $middleware->append([
            SecurityHeaders::class,
        ]);
        $middleware->web(append: [
            Localization::class,
            UpdateLeiterLastAccessed::class,
        ]);
        $middleware->alias([
            'courseNotArchived' => CourseMustNotBeArchived::class,
            'guest' => RedirectIfAuthenticated::class,
            'restoreFormData' => RestoreFormDataFromExpiredSession::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->reportable(function (Throwable $exception) {
            if (!config('app.debug') && app()->bound('sentry')) {
                Integration::captureUnhandledException($exception);
            }
        });

        $exceptions->render(function(Throwable $exception, Request $request) {
            if ($exception instanceof Symfony\Component\HttpKernel\Exception\HttpException && $exception->getMessage() == 'CSRF token mismatch.' && !Auth::check() && $request->method() != 'GET' && !$request->get('noFormRestoring')) {
                $dontFlash = ['password', 'password_confirmation'];
                session()->put(RestoreFormDataFromExpiredSession::KEY, array_filter($request->except($dontFlash), function($input) {
                    // File inputs are not serializable and therefore cannot be saved into the session
                    return !($input instanceof UploadedFile);
                }));
                session()->flash('alert-warning', __('t.errors.session_expired_try_again'));
                return back(302, [], route('login'));
            } else if ($exception instanceof ValidationException || $exception instanceof AuthenticationException) {
                // needed for remembering the active filters while editing an observation
                // and for displaying a flash message when the session expired
                session()->reflash();
            }
            return null;
        });
    })->create();
