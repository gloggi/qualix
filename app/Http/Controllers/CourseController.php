<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseSelectRequest;
use App\Http\Requests\CourseStoreRequest;
use App\Models\Kurs;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $kurs = Kurs::create($request->validated());

        $kurs->users()->attach(Auth::user()->getAuthIdentifier());
        $kurs->save();

        return Redirect::route('home');
    }

    /**
     * Select course to work with from the navigation dropdown.
     *
     * @param CourseSelectRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function select(CourseSelectRequest $request) {
        $validatedData = $request->validated();
        /** @var User $user */
        $user = Auth::user();

        $user->currentKurs = $validatedData['kursId'];
        $user->save();

        return Redirect::route('home');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }
}
