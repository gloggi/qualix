<?php

namespace App\Http\Controllers;

use App\Http\Requests\QKStoreRequest;
use App\Models\Leiter;
use App\Models\QK;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(QKStoreRequest $request) {
        QK::create($request->validated());

        /** @var User $user */
        $user = Auth::user();
        return Redirect::route('admin.qk');
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
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id) {
        DB::transaction(function () use($request, $id) {
            // Check that the user is allowed to delete this QK
            $qk = QK::findOrFail($id);
            if (!Leiter::where('kurs_id', '=', $qk->kurs->id)->where('user_id', '=', Auth::user()->getAuthIdentifier())->exists()) {
                abort(403, __('Das därfsch du nöd'));
            }

            $qk->delete();

            $request->session()->flash('alert-success', __('Quali-Kategorie erfolgreich gelöscht.'));
        });

        return Redirect::route('admin.qk');
    }
}
