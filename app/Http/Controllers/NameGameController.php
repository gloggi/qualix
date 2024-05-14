<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class NameGameController extends Controller
{
    /**
     * Play the name game.
     *
     * @return Response
     */
    public function index()
    {
        return view('nameGame.index');
    }
}
