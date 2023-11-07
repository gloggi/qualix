@extends('layouts.default')

@if(count($feedbackRequirements))
    @section('pagetitle'){{__('t.views.feedback.progress_overview.page_title')}}@endsection
@else
    @section('pagetitle'){{ $feedbackData->name }}@endsection
@endif

@section('wideLayout'){{ json_encode($feedbackRequirements->map->requirement_id->unique()->count() >= 6) }}@endsection

@section('content')

    <b-card>
        <button-print-all-feedbacks :feedbacks="{{ json_encode($feedbacks) }}">
            {{__('t.views.feedback.progress_overview.download_all')}}
            <i class="fas fa-print pl-2"></i>
        </button-print-all-feedbacks>

        @if(count($feedbackRequirements))
            <template #header>{{__('t.views.feedback.progress_overview.requirements_matrix', ['name' => $feedbackData->name])}}</template>
        @else
            <template #header>{{ $feedbackData->name }}</template>
        @endif

        @if($anyResponsibilities)
            <div class="d-flex justify-content-end mb-2">
                <label for="user" class="col-form-label text-md-right mr-2">{{ __('t.views.feedback.progress_overview.view_as') }}</label>
                <multi-select
                    name="user"
                    :value="{{ json_encode("$userId") }}"
                    class=""
                    required
                    :options="{{ json_encode($course->users->map->only('id', 'name')->prepend(['id' => 'all', 'name' => __('t.views.feedback.progress_overview.show_all')])) }}"
                    display-field="name"
                    @update:selected="selected => $window.location = routeUri('feedback.progressOverview', {course: {{ $course->id }}, feedback_data: {{ $feedbackData->id }}, view: selected.id || 'all'})"></multi-select>
            </div>
        @endif

        @if (count($feedbacks))

            @if (count($feedbackRequirements))

                <requirements-matrix
                    :feedback-requirements="{{ json_encode($feedbackRequirements) }}"
                    :feedbacks="{{ json_encode($feedbacks) }}"
                    :all-requirements="{{ json_encode($allRequirements) }}"
                    :all-participants="{{ json_encode($allParticipants) }}"
                    :requirement-statuses="{{ json_encode($course->requirement_statuses) }}"
                    :collaboration-enabled="{{ json_encode(config('app.collaboration.enabled')) }}"></requirements-matrix>

            @else

                <b-list-group flush>
                    @foreach ($feedbacks as $feedback)
                        <b-list-group-item>
                            <div class="d-flex align-items-baseline">
                                <a href="{{ route('participants.detail', ['course' => $course->id, 'participant' => $feedback->participant_id]) }}"><img src="{{ $feedback->participant->image_url != null ? asset(Storage::url($feedback->participant->image_url)) : asset('images/was-gaffsch.svg') }}" class="avatar-small" alt="{{ $feedback->participant->scout_name }}"/></a>
                                <div class="d-flex flex-column flex-grow-1">
                                    <div class="d-flex flex-wrap ml-2">
                                        <a href="{{ route('participants.detail', ['course' => $course->id, 'participant' => $feedback->participant_id]) }}"><strong>{{ $feedback->participant->scout_name }}</strong></a>
                                        <a href="{{ route('feedbackContent.edit', ['course' => $course->id, 'participant' => $feedback->participant_id, 'feedback' => $feedback->id]) }}" target="_blank" title="{{__('t.views.feedback.progress_overview.edit_feedback')}}"><i class="fas fa-pen-to-square px-2"></i></a>
                                        <button-print-feedback :course-id="{{ json_encode($course->id) }}" :participant-id="{{ json_encode($feedback->participant_id) }}" :feedback-id="{{ json_encode($feedback->id) }}">
                                            <i class="fas fa-print pl-2"></i>
                                        </button-print-feedback>
                                    </div>
                                    @if(count($feedback->users))
                                        <div class="mw-80 ml-2">{{__('t.models.feedback.users')}}: {{ $feedback->users->map->name->join(', ') }}</div>
                                    @endif
                                </div>
                            </div>
                        </b-list-group-item>
                    @endforeach
                </b-list-group>

            @endif

        @else

            {{ __('t.views.feedback.progress_overview.none_assigned_to_user', ['user' => $user?->name]) }} <a href="{{ route('admin.feedbacks.edit', ['course' => $course->id, 'feedback_data' => $feedbackData->id, 'highlight' => 'assignments']) }}">{{ __('t.views.feedback.progress_overview.edit_responsibles') }}</a>

        @endif

    </b-card>

@endsection
