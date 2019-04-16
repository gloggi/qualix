<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvitationClaimRequest;
use App\Http\Requests\InvitationRequest;
use App\Mail\InvitationMail;
use App\Models\Einladung;
use App\Models\Kurs;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class InvitationController extends Controller {

    /**
     * Store a newly created resource in storage.
     *
     * @param InvitationRequest $request
     * @param Kurs $kurs
     * @return RedirectResponse
     */
    public function store(InvitationRequest $request, Kurs $kurs) {
        $data = $request->validated();

        do {
            $token = Str::random();
        } while (Einladung::where('token', '=', $token)->exists());

        $einladung = Einladung::firstOrCreate(['kurs_id' => $kurs->id, 'email' => $data['email']], array_merge($data, ['kurs_id' => $kurs->id, 'token' => $token]));

        Mail::to($data['email'])->send(new InvitationMail($einladung));

        return Redirect::route('admin.equipe', ['kurs' => $kurs->id]);
    }

    /**
     * View an invitation received by email.
     *
     * @param Request $request
     * @param $token
     * @return Response
     */
    public function index(Request $request, $token) {
        $invitation = Einladung::where('token', '=', $token)->firstOrFail();

        /** @var User $user */
        $user = Auth::user();
        if ($user->kurse()->find($invitation->kurs->id)) {
            return view('invitation-already-in-course', ['invitation' => $invitation]);
        }

        return view('invitation', ['invitation' => $invitation]);
    }

    /**
     * Claim an invitation by a given token.
     *
     * @param InvitationClaimRequest $request
     * @return RedirectResponse
     */
    public function claim(InvitationClaimRequest $request) {
        return DB::transaction(function () use ($request) {
            $invitation = Einladung::where('token', '=', $request->validated()['token'])->firstOrFail();

            $invitation->kurs->users()->attach(Auth::user()->getAuthIdentifier());

            $invitation->delete();

            $request->session()->flash('alert-success', __('Einladung angenommen. Du bist jetzt in der Equipe von :kursname', ['kursname' => $invitation->kurs->name]));

            return Redirect::route('index', ['kurs' => $invitation->kurs->id]);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Kurs $kurs
     * @param $email
     * @return RedirectResponse
     */
    public function destroy(Request $request, Kurs $kurs, $email) {
        Einladung::where('kurs_id', '=', $kurs->id)->where('email', '=', $email)->delete();

        return Redirect::route('admin.equipe', ['kurs' => $kurs->id]);
    }
}
