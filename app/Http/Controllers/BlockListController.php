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
    public function crib()
    {
        return view('crib');
    }
}
