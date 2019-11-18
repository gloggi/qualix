<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class Localization {

    public function handle(Request $request, Closure $next) {
        if (!Session::has('locale')) {
            if (in_array($request->getLocale(), config('app.supported_locales'))) {
                Session::put('locale', $request->getLocale());
            } else {
                Session::put('locale', Lang::getFallback());
            }
        }
        App::setlocale(Session::get('locale'));
        return $next($request);
    }

}
