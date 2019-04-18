<?php

namespace App\Http\Controllers;

use App\Models\Kurs;
use App\Models\TN;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TNDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param Kurs $kurs
     * @param TN $tn
     * @return Response
     */
    public function index(Request $request, Kurs $kurs, TN $tn)
    {
        return view('tn-detail', ['tn' => $tn]);
    }
}
