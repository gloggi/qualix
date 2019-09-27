<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class BlockListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('blocks');
    }

    /**
     * Display the crib page which visualizes connections between blocks and requirements.
     *
     * @return Response
     */
    public function crib()
    {
        return view('crib');
    }
}
