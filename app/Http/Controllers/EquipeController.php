<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class EquipeController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        return view('admin.equipe.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Course $course
     * @param User $user
     * @return RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, Course $course, User $user) {
        try {

            DB::transaction(function () use ($course, $user) {

                $course->users()->detach($user->id);

                if (!$course->users()->exists()) {
                    throw new \LogicException(__('t.views.admin.equipe.cannot_delete_last_leiter'));
                }

            });

            $request->session()->flash('alert-success', __('t.views.admin.equipe.delete_success', ['name' => $user->name]));

            if ($user->id === Auth::user()->getAuthIdentifier()) {
                return Redirect::route('home');
            }

        } catch (\LogicException $e) {
            $request->session()->flash('alert-danger', $e->getMessage());
        }

        return Redirect::route('admin.equipe', ['course' => $course->id]);
    }
}
