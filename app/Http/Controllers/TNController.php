<?php

namespace App\Http\Controllers;

use App\Models\TN;
use App\Models\Kurs;
use App\Http\Requests\TNStoreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class TNController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.tn.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TNStoreRequest $request, Kurs $kurs)
    {
        $validatedData = $request->validated();
        $tn = new TN($validatedData);
        $tn->kurs_id = Auth::user()->lastAccessedKurs->id;

        if (isset($validatedData['bild'])) {
            $path = $validatedData['bild']->store('public/images');
            $tn->bild_url = $path;
        }

        $tn->save();

        return Redirect::route('admin.tn', ['kurs' => $kurs->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TN  $tn
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $tn = $request->tn;
        return view('admin.tn.show', compact('tn'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TN  $tn
     * @return \Illuminate\Http\Response
     */
    public function edit(Kurs $kurs, TN $tn)
    {
        return view('admin.tn.edit', ['tn' => $tn]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TN  $tn
     * @return \Illuminate\Http\Response
     */
    public function update(TNStoreRequest $request, Kurs $kurs, TN $tn)
    {
        $validatedData = $request->validated();

        if (isset($validatedData['bild'])) {
            $path = $validatedData['bild']->store('public/images');
            $validatedData['bild_url'] = $path;
            Storage::delete($tn->bild_url);
        }
        $tn->update($validatedData);

        $request->session()->flash('alert-success', __('TN erfolgreich gespeichert.'));
        return Redirect::route('admin.tn', ['kurs' => $kurs->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TN  $tn
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Kurs $kurs, TN $tn)
    {
        if ($tn->bild_url) {
            Storage::delete($tn->bild_url);
        }
        $tn->delete();
        $request->session()->flash('alert-success', __('TN erfolgreich gelöscht.'));
        return Redirect::route('admin.tn', ['kurs' => $kurs->id]);
    }
}
