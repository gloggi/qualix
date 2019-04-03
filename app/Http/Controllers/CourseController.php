<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseSelectRequest;
use App\Http\Requests\CourseStoreRequest;
use App\Http\Requests\CourseUpdateRequest;
use App\Models\Kurs;
use App\Models\Leiter;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class CourseController extends Controller {
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('admin.newcourse');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CourseStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CourseStoreRequest $request) {
        DB::transaction(function () use ($request) {
            $kurs = Kurs::create($request->validated());

            $kurs->users()->attach(Auth::user()->getAuthIdentifier());
            $kurs->save();
        });

        return Redirect::route('home');
    }

    /**
     * Select course to work with from the navigation dropdown.
     *
     * @param CourseSelectRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function select(CourseSelectRequest $request) {
        DB::transaction(function () use ($request) {
            $validatedData = $request->validated();
            /** @var User $user */
            $user = Auth::user();

            $user->currentKurs = $validatedData['kursId'];
            $user->save();
        });

        return Redirect::route('home');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit() {
        /** @var User $user */
        $user = Auth::user();
        $kurs = Kurs::find($user->currentKurs->id);

        return view('admin.editcourse', ['kurs' => $kurs]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CourseUpdateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CourseUpdateRequest $request) {
        DB::transaction(function () use ($request) {
            $validatedData = $request->validated();

            // Check that the user is allowed to change this kurs
            if (!Leiter::where('kurs_id', '=', $validatedData['id'])->where('user_id', '=', Auth::user()->getAuthIdentifier())->exists()) {
                abort(403, __('Das därfsch du nöd'));
            }

            Kurs::find($validatedData['id'])->update($validatedData);

            $request->session()->flash('alert-success', __('Kursdetails erfolgrich gspeicheret'));
        });

        return Redirect::route('admin.kurs');
    }
}
