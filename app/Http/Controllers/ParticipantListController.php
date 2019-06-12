<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class ParticipantListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('participants');
    }
}
