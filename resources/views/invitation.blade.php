@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('t.views.invitation.title', ['courseName' => $invitation->course->name])])

        @component('components.form', ['route' => 'invitation.claim'])

            @component('components.form.hiddenInput', ['name' => 'token', 'value' => $invitation->token])@endcomponent

            @component('components.form.text', ['classes' => 'mb-0'])
                <p>{{__('t.views.invitation.is_email_yours', ['email' => $invitation->email])}}</p>
            @endcomponent

            @component('components.form.submit', ['label' => __('t.views.invitation.accept_invitation')])
                <a class="btn btn-link" href="{{ route('home') }}">
                    {{ __('t.views.invitation.decline_invitation') }}
                </a>
            @endcomponent

        @endcomponent

    @endcomponent

@endsection
