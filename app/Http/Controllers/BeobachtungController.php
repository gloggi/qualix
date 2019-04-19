<?php

namespace App\Http\Controllers;

use App\Http\Requests\BeobachtungRequest;
use App\Models\Beobachtung;
use App\Models\Block;
use App\Models\Kurs;
use App\Models\TN;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class BeobachtungController extends Controller {
    /**
     * Display a form to create a new resource.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request) {
        return view('beobachtung.new', ['tn_id' => $request->input('tn'), 'block_id' => $request->input('block')]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param BeobachtungRequest $request
     * @param Kurs $kurs
     * @return RedirectResponse
     */
    public function store(BeobachtungRequest $request, Kurs $kurs) {
        $data = $request->validated();
        DB::transaction(function() use ($request, $kurs, $data) {
            $tn_ids = explode(',', $data['tn_ids']);
            $ma_ids = array_filter(explode(',', $data['ma_ids']));
            $qk_ids = array_filter(explode(',', $data['qk_ids']));

            foreach ($tn_ids as $tn_id) {
                $beobachtung = Beobachtung::create(array_merge($data, ['tn_id' => $tn_id, 'kurs_id' => $kurs->id, 'user_id' => Auth::user()->getAuthIdentifier()]));
                $beobachtung->mas()->attach($ma_ids);
                $beobachtung->qks()->attach($qk_ids);
            }

            if (count($tn_ids) > 1) {
                $request->session()->flash('alert-success', __('Beobachtungen erfasst. Mässi!'));
            } else {
                $tn = TN::find($tn_ids[0]);
                $request->session()->flash('alert-success', __('Beobachtung erfasst. Mässi!') . ' <a href="' . route('tn.detail', ['kurs' => $kurs->id, 'tn' => $tn->id]) . '">' . __('Zurück zu :name', ['name' => $tn->pfadiname]) . ' <i class="fas fa-arrow-right"></i></a>');
            }
        });

        return Redirect::route('beobachtung.neu', ['kurs' => $kurs->id, 'tn' => $data['tn_ids'], 'block' => $data['block_id']]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Kurs $kurs
     * @param Beobachtung $beobachtung
     * @return Response
     */
    public function edit(Kurs $kurs, Beobachtung $beobachtung) {
        return view('beobachtung.edit', ['beobachtung' => $beobachtung]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param BeobachtungRequest $request
     * @param Kurs $kurs
     * @param Beobachtung $beobachtung
     * @return RedirectResponse
     */
    public function update(BeobachtungRequest $request, Kurs $kurs, Beobachtung $beobachtung) {
        DB::transaction(function () use ($request, $beobachtung) {
            $data = $request->validated();
            $beobachtung->update($data);

            $beobachtung->mas()->detach();
            $beobachtung->mas()->attach(array_filter(explode(',', $data['ma_ids'])));

            $beobachtung->qks()->detach();
            $beobachtung->qks()->attach(array_filter(explode(',', $data['qk_ids'])));
        });

        $request->session()->flash('alert-success', __('Beobachtung aktualisiert.'));

        return Redirect::route('tn.detail', ['kurs' => $kurs->id, 'tn' => $beobachtung->tn->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Kurs $kurs
     * @param Block $block
     * @return RedirectResponse
     */
    public function destroy(Request $request, Kurs $kurs, Beobachtung $beobachtung) {
        $beobachtung->delete();
        $request->session()->flash('alert-success', __('Beobachtung gelöscht.'));
        return Redirect::back();
    }

    /**
     * Show an overview table with info about which user has made how many observations about which TN.
     *
     * @param Request $request
     * @param Kurs $kurs
     * @return Response
     */
    public function overview(Request $request, Kurs $kurs) {
        return view('ueberblick', ['tns' => $kurs->tns->all()]);
    }
}
