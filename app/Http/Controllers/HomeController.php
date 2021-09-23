<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
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
     * @param UserRequest $request
     * @return RedirectResponse
     */
    public function updateUser(UserRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($request->file('image') && $user->image_url) {
            Storage::delete($user->image_url);
        }

        $user->update($request->validated());

        $request->session()->flash('alert-success', __('t.views.user_settings.edit_success'));

        return Redirect::route('home');
    }

    /**
     * Show a page for refreshing the CSRF token on an expired form.
     *
     * @return View
     */
    public function refreshCsrf() {
        return view('pages.refreshCsrf');
    }

}
