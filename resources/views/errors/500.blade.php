@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Server Error')])
        @if(app()->bound('sentry') && app('sentry')->getLastEventId() && env('SENTRY_USER_FEEDBACK_URL'))

            <h5>{{__('t.views.error_form.it_looks_like_we_are_having_issues')}}</h5>

            <p>{{__('t.views.error_form.our_team_has_been_notified')}}</p>

            @component('components.form', ['route' => ['errorReport.submit'], 'method' => 'POST'])

                @component('components.form.hiddenInput', ['name' => 'eventId', 'value' => app('sentry')->getLastEventId()])@endcomponent

                @component('components.form.hiddenInput', ['name' => 'previousUrl', 'value' => url()->previous()])@endcomponent

                @component('components.form.textInput', ['name' => 'name', 'label' => __('t.models.user.name'), 'required' => true, 'autofocus' => true])@endcomponent

                @component('components.form.textInput', ['name' => 'email', 'label' => __('t.models.user.email'), 'required' => true])@endcomponent

                @component('components.form.textareaInput', ['name' => 'description', 'label' => __('t.views.error_form.what_happened'), 'required' => true, 'placeholder' => __('t.views.error_form.what_happened_example')])@endcomponent

                @component('components.form.submit', ['label' => __('t.views.error_form.send_description')])
                        <a class="btn btn-link mb-1" href="{{ url()->previous() }}">
                            {{ __('t.views.error_form.back_without_sending_report') }}
                        </a>
                @endcomponent

            @endcomponent

        @else
            <p>{{__('t.views.error_form.please_try_again_later')}}</p>
        @endif
    @endcomponent

@endsection
