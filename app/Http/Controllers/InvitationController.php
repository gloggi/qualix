<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvitationRequest;
use App\Mail\InvitationMail;
use App\Models\Einladung;
use App\Models\Kurs;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
     * @param $email
     * @return void
     */
    public function destroy(Request $request, Kurs $kurs, $email) {
        Einladung::where('kurs_id', '=', $kurs->id)->where('email', '=', $email)->delete();

        return Redirect::route('admin.equipe', ['kurs' => $kurs->id]);
    }
}
