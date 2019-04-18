<?php

namespace App\Http\Controllers;

use App\Http\Requests\BeobachtungRequest;
use App\Models\Beobachtung;
use App\Models\Block;
use App\Models\Kurs;
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
        DB::transaction(function() use ($request, $kurs) {
            $data = $request->validated();
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
                $request->session()->flash('alert-success', __('Beobachtung erfasst. Mässi!'));
            }
        });

        return Redirect::back();
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
        $beobachtung->update($request->validated());

        $request->session()->flash('alert-success', __('Beobachtung aktualisiert.'));

        return Redirect::back();
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
        return Redirect::route('admin.bloecke', ['kurs' => $kurs->id]);
    }
}
