@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Einladung in :kursname', ['kursname' => $invitation->kurs->name])])

        @component('components.form', ['route' => 'invitation.claim'])

            @component('components.form.hiddenInput', ['name' => 'token', 'value' => $invitation->token])@endcomponent

            @component('components.form.text')
                <p>{{__('Gehört dir die Mailadresse :email?', ['email' => $invitation->email])}}</p>
            @endcomponent

            @component('components.form.submit', ['label' => __('Ja, Einladung annehmen')])
                <a class="btn btn-link" href="{{ route('home') }}">
                    {{ __('Nein, diese Einladung ist nicht für mich') }}
                </a>
            @endcomponent


        @endcomponent

    @endcomponent

@endsection
