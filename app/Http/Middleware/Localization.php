<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class Localization {

    /**
     * Set up the user interface language according to the visitor's preferences from session or browser settings.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        if (!Session::has('locale')) {
            Session::put('locale', $request->getPreferredLanguage(config('app.supported_locales')));
        }
        App::setLocale(Session::get('locale'));
        return $next($request);
    }

}
