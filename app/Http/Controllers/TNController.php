<?php

namespace App\Http\Controllers;

use App\Http\Requests\TNStoreRequest;
use App\Models\Kurs;
use App\Models\TN;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class TNController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('admin.tn.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TNStoreRequest $request
     * @param Kurs $kurs
     * @return RedirectResponse
     */
    public function store(TNStoreRequest $request, Kurs $kurs)
    {
        TN::create(array_merge($request->validated(), ['kurs_id' => $kurs->id]));

        return Redirect::route('admin.tn', ['kurs' => $kurs->id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param TN $tn
     * @return Response
     */
    public function edit(Kurs $kurs, TN $tn)
    {
        return view('admin.tn.edit', ['tn' => $tn]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TNStoreRequest $request
     * @param Kurs $kurs
     * @param TN $tn
     * @return RedirectResponse
     */
    public function update(TNStoreRequest $request, Kurs $kurs, TN $tn)
    {
        if ($request->file('bild') && $tn->bild_url) {
            Storage::delete($tn->bild_url);
        }

        $tn->update($request->validated());

        $request->session()->flash('alert-success', __('TN erfolgreich gespeichert.'));
        return Redirect::route('admin.tn', ['kurs' => $kurs->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Kurs $kurs
     * @param TN $tn
     * @return RedirectResponse
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
