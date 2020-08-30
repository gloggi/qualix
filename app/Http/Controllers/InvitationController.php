<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvitationClaimRequest;
use App\Http\Requests\InvitationRequest;
use App\Mail\InvitationMail;
use App\Models\Course;
use App\Models\Invitation;
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
     * @param Course $course
     * @return RedirectResponse
     */
    public function store(InvitationRequest $request, Course $course) {
        $data = $request->validated();

        do {
            $token = Str::random();
        } while (Invitation::where('token', '=', $token)->exists());

        $invitation = Invitation::firstOrCreate(['course_id' => $course->id, 'email' => $data['email']], array_merge($data, ['course_id' => $course->id, 'token' => $token]));

        Mail::to($data['email'])->send(new InvitationMail($invitation));

        $request->session()->flash('alert-success', __('t.views.admin.equipe.invitation_email_sent', ['email' => $invitation->email]));

        return Redirect::route('admin.equipe', ['course' => $course->id]);
    }

    /**
     * View an invitation received by email.
     *
     * @param Request $request
     * @param $token
     * @return Response
     */
    public function index(Request $request, $token) {
        $invitation = Invitation::where('token', '=', $token)->firstOrFail();

        /** @var User $user */
        $user = Auth::user();
        if ($user->courses()->find($invitation->course->id)) {
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
        try {
            return DB::transaction(function () use ($request) {
                $invitation = Invitation::where('token', '=', $request->validated()['token'])->firstOrFail();

                /** @var User $user */
                $user = Auth::user();
                if ($user->courses()->find($invitation->course->id)) {
                    return Redirect::route('admin.equipe', ['course' => $invitation->course->id]);
                }

                $invitation->course->users()->attach($user->getAuthIdentifier());

                $invitation->delete();

                $request->session()->flash('alert-success', __('t.views.invitation.accept_success', ['courseName' => $invitation->course->name]));

                return Redirect::route('index', ['course' => $invitation->course->id]);
            });
        } catch (\Exception $e) {

            $request->session()->flash('alert-danger', __('t.views.invitation.error'));
            return Redirect::route('home');

        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Course $course
     * @param $email
     * @return RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, Course $course, $email) {
        $invitation = Invitation::where('course_id', '=', $course->id)->where('email', '=', $email)->firstOrFail();
        $invitation->delete();

        $request->session()->flash('alert-success', __('t.views.admin.equipe.delete_invitation_success', ['email' => $invitation->email]));
        return Redirect::route('admin.equipe', ['course' => $course->id]);
    }
}
