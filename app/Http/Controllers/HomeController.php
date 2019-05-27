<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Show Welcome-Page.
     *
     * @return View
     */
    public function index()
    {
        return view('pages.welcome');
    }

    /**
     * show User.
     *
     * @return View
     */
    public function editUser()
    {
        $user = Auth::user();

        return view('pages.edit-user', ['user' => $user]);
    }

    /**
     * edit User.
     *
     * @return Redirect
     */
    public function updateUser(UserRequest $request)
    {
        $user = Auth::user();

        if ($request->file('bild') && $user->bild_url) {
            Storage::delete($user->bild_url);
        }

        $user->update($request->validated());

        $request->session()->flash('alert-success', __('User erfolgreich gespeichert.'));

        return Redirect::route('home');
    }


}
