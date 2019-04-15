<?php

namespace App\Http\Controllers;

use App\Models\Kurs;
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
     * Claim an invitation received by email.
     *
     * @param Request $request
     * @param $token
     */
    public function claimInvitation(Request $request, $token) {
        // TODO
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Kurs $kurs
     * @param User $user
     * @return RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, Kurs $kurs, User $user) {
        try {

            DB::transaction(function () use ($kurs, $user) {

                Kurs::find($kurs->id)->users()->detach($user->id);

                if (!Kurs::find($kurs->id)->users()->exists()) {
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

        return Redirect::route('admin.equipe', ['kurs' => $kurs->id]);
    }
}
