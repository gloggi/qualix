<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;

class LocalizationController extends Controller {

    /**
     * Switch the locale to a given value.
     *
     * @param $locale string desired locale
     * @return Response redirects back to where the user came from
     */
    public function select($locale) {
        Session::put('locale', $locale);
        return redirect()->back();
    }

}
