<?php

namespace App\Http\Controllers;

use App\Http\Requests\MAStoreRequest;
use App\Http\Requests\MAUpdateRequest;
use App\Models\Kurs;
use App\Models\MA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class MAController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('admin.ma');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MAStoreRequest $request
     * @param Kurs $kurs
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(MAStoreRequest $request, Kurs $kurs) {
        MA::create(array_merge($request->validated(), ['kurs_id' => $kurs->id]));
        return Redirect::route('admin.ma', ['kurs' => $kurs->id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Kurs $kurs
     * @param MA $ma
     * @return \Illuminate\Http\Response
     */
    public function edit(Kurs $kurs, MA $ma) {
        return view('admin.ma-edit', ['ma' => $ma]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MAUpdateRequest $request
     * @param Kurs $kurs
     * @param MA $ma
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(MAUpdateRequest $request, Kurs $kurs, MA $ma) {
        $ma->update($request->validated());
        $request->session()->flash('alert-success', __('Mindestanforderung erfolgreich gespeichert.'));
        return Redirect::route('admin.ma', ['kurs' => $kurs->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Kurs $kurs
     * @param MA $ma
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, Kurs $kurs, MA $ma) {
        $ma->delete();
        $request->session()->flash('alert-success', __('Mindestanforderung erfolgreich gelöscht.'));
        return Redirect::route('admin.ma', ['kurs' => $kurs->id]);
    }
}
