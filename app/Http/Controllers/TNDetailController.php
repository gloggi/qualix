<?php

namespace App\Http\Controllers;

use App\Models\Beobachtung;
use App\Models\Kurs;
use App\Models\MA;
use App\Models\QK;
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
        $beobachtungen = $tn->beobachtungen;

        $ma = $request->input('ma');
        if ($ma != null) {
            $beobachtungen = $beobachtungen->filter(function (Beobachtung $beobachtung, $key) use ($ma) {
                return $beobachtung->mas->map(function (MA $ma) { return $ma->id; })->contains($ma);
            });
        }

        $qk = $request->input('qk');
        if ($qk != null) {
            $beobachtungen = $beobachtungen->filter(function (Beobachtung $beobachtung, $key) use ($qk) {
                return $beobachtung->qks->map(function (QK $qk) { return $qk->id; })->contains($qk);
            });
        }

        return view('tn-detail', ['tn' => $tn, 'beobachtungen' => $beobachtungen, 'ma' => $ma, 'qk' => $qk]);
    }
}
