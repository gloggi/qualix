@extends('layouts.default')

@section('pagetitle'){{__('t.views.feedback_content.page_title', ['feedbackName' => $feedback->name, 'participantName' => $participant->scout_name]) }}@endsection

@section('content')

    <b-card body-class="container-fluid">
        <template #header>{{__('t.views.feedback_content.title')}}</template>

        @component('feedbackContent.header', ['feedback' => $feedback, 'participant' => $participant, 'course' => $course])@endcomponent

        <form-feedback-content
            :action="['feedbackContent.update', { course: {{ $course->id }}, participant: {{ $participant->id }}, feedback: {{ $feedback->id }}, noFormRestoring: 1 }]"
            course-id="{{ $course->id }}"
            feedback-data-id="{{ $feedback->feedback_data_id }}"
            :feedback-contents="{{ json_encode($feedback->contents) }}"
            :observations="{{ json_encode($observations) }}"
            :requirements="{{ json_encode($course->requirements) }}"
            :categories="{{ json_encode($course->categories) }}"
            :authors="{{ json_encode($course->users->map->only('id', 'name')) }}"
            :blocks="{{ json_encode($course->blocks) }}"
            :requirement-statuses="{{ json_encode($course->requirement_statuses) }}"
            :show-requirements="{{ $course->uses_requirements ? 'true' : 'false' }}"
            :show-categories="{{ $course->uses_categories ? 'true' : 'false' }}"
            :show-impression="{{ $course->uses_impressions ? 'true' : 'false' }}"
            :collaboration-key="{{ json_encode(config('app.collaboration.enabled') ? $feedback->collaborationKey : null) }}">

            <div>
                <b-button variant="link" class="px-0 mr-3" href="{{ route('participants.detail', ['course' => $course->id, 'participant' => $participant->id]) }}">
                    <i class="fas fa-arrow-left"></i> {{__('t.views.feedback_content.back_to_participant', ['name' => $participant->scout_name])}}
                </b-button>

                <b-button variant="link" class="px-0" href="{{ route('feedback.progressOverview', ['course' => $course->id, 'feedback_data' => $feedback->feedback_data_id]) }}">
                    <i class="fas fa-arrow-left"></i> {{__('t.views.feedback_content.back_to_feedback_overview')}}
                </b-button>
            </div>

        </form-feedback-content>

    </b-card>

@endsection
