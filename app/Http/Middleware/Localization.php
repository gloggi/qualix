<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class Localization {

    /**
     * @var array maps app locales (like we use them) to PHP locales (for setlocale).
     */
    protected $localeMap = [
        'de' => 'de_CH.utf8',
        'fr' => 'fr_CH.utf8',
    ];

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

        $this->setAppLocale(Session::get('locale'));

        /** @var Response $response */
        $response = $next($request);

        return $response->header('Content-Language', App::getLocale());
    }

    /**
     * Sets the given locale in the whole application.
     *
     * @param $locale string a supported app locale
     */
    protected function setAppLocale($locale) {
        App::setLocale($locale);
        $phpLocale = array_has($this->localeMap, $locale) ? $this->localeMap[$locale] : $locale;
        setlocale(LC_ALL, $phpLocale);
    }

}
