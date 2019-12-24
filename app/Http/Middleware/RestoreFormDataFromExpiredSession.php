<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class RestoreFormDataFromExpiredSession
{
    const KEY = '_restorable_form_data';

    /**
     * In case the user entered data in a form, but was logged out in the meantime,
     * his data is saved in the session when he submits the request. This middleware
     * restores that saved form data once the user has logged back in.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (session()->has(self::KEY)) {
            session()->now('_old_input', session(self::KEY));
            session()->forget(self::KEY);
            session()->now('alert-warning', __('t.errors.form_data_restored_please_submit_again'));
        }
        return $next($request);
    }
}
