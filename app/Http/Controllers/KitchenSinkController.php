<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class KitchenSinkController extends Controller
{
    /**
     * Show the kitchen sink page as a placeholder.
     *
     * @return View
     */
    public function index()
    {
        return view('pages.kitchensink');
    }
}
