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
        $supportedLocales = config('app.supported_locales');
        if (!Session::has('locale') || !in_array(Session::get('locale'), $supportedLocales)) {
            Session::put('locale', $request->getPreferredLanguage($supportedLocales));
        }
        App::setLocale(Session::get('locale'));

        $response = $next($request);

        return $response->header('Content-Language', App::getLocale());
    }

}
