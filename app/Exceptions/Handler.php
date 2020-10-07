<?php

namespace App\Exceptions;

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\RestoreFormDataFromExpiredSession;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\UploadedFile;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Throwable $exception
     * @return void
     * @throws Throwable
     */
    public function report(Throwable $exception) {
        if (!env('APP_DEBUG') && app()->bound('sentry') && $this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof TokenMismatchException && !Auth::check() && $request->method() != 'GET') {
            return $this->preserveSubmittedFormData($request);
        }
        if ($exception instanceof ValidationException || $exception instanceof AuthenticationException) {
            // needed for remembering the active filters while editing an observation
            // and for displaying a flash message when the session expired
            session()->reflash();
        }
        return parent::render($request, $exception);
    }

    /**
     * The user submitted a form but wasn't authenticated. Probably the session expired.
     * Preserve the form data so it can be restored once the user logs back in.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function preserveSubmittedFormData($request) {
        session()->put(RestoreFormDataFromExpiredSession::KEY, array_filter($request->except($this->dontFlash), function($input) {
            // File inputs are not serializable and therefore cannot be saved into the session
            return !($input instanceof UploadedFile);
        }));
        session()->flash('alert-warning', __('t.errors.session_expired_try_again'));

        return Redirect::back(302, [], app(Authenticate::class)->redirectTo($request));
    }
}
