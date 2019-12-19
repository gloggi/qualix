<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class KeepOldInputInFlash
{
    const KEEP_OLD_INPUT_IN_FLASH = 'KEEP_OLD_INPUT_IN_FLASH';

    /**
     * Keep the '_old_input' flash key in the session for the next request.
     * This is used in all login-related pages (except logout, technically)
     * so that when the user submits a form after a long time (so long that
     * his session ran out), we can redisplay the form with the entered data
     * after the user logged in again.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (session()->has(self::KEEP_OLD_INPUT_IN_FLASH)) {
            session()->keep('_old_input');
            session()->keep(self::KEEP_OLD_INPUT_IN_FLASH);
        }
        return $next($request);
    }
}
