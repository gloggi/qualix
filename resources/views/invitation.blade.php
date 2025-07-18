@extends('layouts.default')

@section('pagetitle'){{__('t.views.invitation.page_title') }}@endsection

@section('content')

    <b-card>
        <template #header>{{__('t.views.invitation.title', ['courseName' => $invitation->course->name])}}</template>

        <form-basic action="invitation.claim">

            <input-hidden name="token" model-value="{{ $invitation->token }}"></input-hidden>

            <row-text class="mb-0">
                <p>{{__('t.views.invitation.is_email_yours', ['email' => $invitation->email])}}</p>
            </row-text>

            <button-submit label="{{__('t.views.invitation.accept_invitation')}}">
                <a class="btn btn-link" href="{{ route('home') }}">
                    {{ __('t.views.invitation.decline_invitation') }}
                </a>
            </button-submit>

        </form-basic>

    </b-card>

@endsection
