<?php

namespace App\Exceptions;

use App\Http\Middleware\RestoreFormDataFromExpiredSession;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\UploadedFile;

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
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof AuthenticationException && $request->method() != 'GET') {
            $this->preserveSubmittedFormData($request);
        }
        return parent::render($request, $exception);
    }

    /**
     * The user submitted a form but wasn't authenticated. Probably the session expired.
     * Preserve the form data so it can be restored once the user logs back in.
     *
     * @param \Illuminate\Http\Request $request
     */
    protected function preserveSubmittedFormData($request) {
        session()->put(RestoreFormDataFromExpiredSession::KEY, array_filter($request->except($this->dontFlash), function($input) {
            // File inputs are not serializable and therefore cannot be saved into the session
            return !($input instanceof UploadedFile);
        }));
        session()->flash('alert-warning', __('t.errors.session_expired_try_again'));
    }
}
