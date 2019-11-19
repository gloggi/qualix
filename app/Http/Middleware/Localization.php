<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class Localization {

    public function handle(Request $request, Closure $next) {
        if (!Session::has('locale')) {
            Session::put('locale', $request->getPreferredLanguage(config('app.supported_locales')));
        }
        App::setlocale(Session::get('locale'));
        return $next($request);
    }

}
