<?php

namespace App\Http\Controllers;

use App\Http\Requests\QKDeleteRequest;
use App\Http\Requests\QKStoreRequest;
use App\Models\Kurs;
use App\Models\QK;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class QKController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        /** @var User $user */
        $user = Auth::user();
        return view('admin.qk', ['kurs' => $user->currentKurs]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param QKStoreRequest $request
     * @param Kurs $kurs
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(QKStoreRequest $request, Kurs $kurs) {
        QK::create($request->validated());
        return Redirect::route('admin.qk', ['kurs' => $kurs->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param QKDeleteRequest $request
     * @param QK $qk
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(QKDeleteRequest $request, Kurs $kurs, QK $qk) {
        $qk->delete();
        $request->session()->flash('alert-success', __('Quali-Kategorie erfolgreich gelöscht.'));
        return Redirect::route('admin.qk', ['kurs' => $kurs->id]);
    }
}
