@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.invitation.title', ['courseName' => $invitation->course->name])}}</template>

        @component('components.form', ['route' => 'invitation.claim'])

            <input-hidden name="token" value="{{ $invitation->token }}"></input-hidden>

            <row-text class="mb-0">
                <p>{{__('t.views.invitation.is_email_yours', ['email' => $invitation->email])}}</p>
            </row-text>

            <button-submit label="{{__('t.views.invitation.accept_invitation')}}">
                <a class="btn btn-link" href="{{ route('home') }}">
                    {{ __('t.views.invitation.decline_invitation') }}
                </a>
            </button-submit>

        @endcomponent

    </b-card>

@endsection
