@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('Server Error')}}</template>

        @if(app()->bound('sentry') && app('sentry')->getLastEventId() && env('SENTRY_USER_FEEDBACK_URL'))

            <h5>{{__('t.views.error_form.it_looks_like_we_are_having_issues')}}</h5>

            <p>{{__('t.views.error_form.our_team_has_been_notified')}}</p>

            @component('components.form', ['route' => ['errorReport.submit'], 'method' => 'POST'])

                <input-hidden name="eventId" value="{{ app('sentry')->getLastEventId() }}"></input-hidden>

                <input-hidden name="previousUrl" value="{{ url()->previous() }}"></input-hidden>

                <input-text name="name" label="{{__('t.models.user.name')}}" required autofocus></input-text>

                <input-text name="email" label="{{__('t.models.user.email')}}" required></input-text>

                <input-textarea name="description" label="{{__('t.views.error_form.what_happened')}}" placeholder="{{__('t.views.error_form.what_happened_example')}}" required></input-textarea>

                <button-submit label="{{__('t.views.error_form.send_description')}}">
                    <a class="btn btn-link mb-1" href="{{ url()->previous() }}">
                        {{ __('t.views.error_form.back_without_sending_report') }}
                    </a>
                </button-submit>

            @endcomponent

        @else
            <p>{{__('t.views.error_form.please_try_again_later')}}</p>
        @endif

    </b-card>

@endsection
