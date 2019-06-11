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
        return view('admin.equipe');
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

                Course::find($course->id)->users()->detach($user->id);

                if (!Course::find($course->id)->users()->exists()) {
                    throw new \LogicException('Cannot delete the last Leiter in the course');
                }

            });

            $request->session()->flash('alert-success', __('Leiterrole erfolgreich entfernt.'));

            if ($user->id === Auth::user()->getAuthIdentifier()) {
                return Redirect::route('home');
            }

        } catch (\LogicException $e) {
            $request->session()->flash('alert-danger', __('Mindestens ein Equipenmitglied muss im Kurs bleiben.'));
        }

        return Redirect::route('admin.equipe', ['course' => $course->id]);
    }
}
