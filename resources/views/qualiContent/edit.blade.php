@extends('layouts.default')

@section('content')

    <b-card body-class="container-fluid">
        <template #header>{{__('t.views.quali_content.title')}}</template>

        @component('qualiContent.header', ['quali' => $quali, 'participant' => $participant, 'course' => $course])@endcomponent

        <form-quali-content
            :action="['qualiContent.update', { course: {{ $course->id }}, participant: {{ $participant->id }}, quali: {{ $quali->id }} }]"
            course-id="{{ $course->id }}"
            :quali-contents="{{ json_encode($quali->contents) }}"
            :observations="{{ json_encode($observations) }}"
            :requirements="{{ json_encode($course->requirements) }}"
            :categories="{{ json_encode($course->categories) }}"
            :show-requirements="{{ $course->uses_requirements ? 'true' : 'false' }}"
            :show-categories="{{ $course->uses_categories ? 'true' : 'false' }}"
            :show-impression="{{ $course->uses_impressions ? 'true' : 'false' }}">

            <b-button variant="link" class="px-0" href="{{ route('participants.detail', ['course' => $course->id, 'participant' => $participant->id]) }}">
                <i class="fas fa-arrow-left"></i> {{__('t.views.quali_content.back_to_participant', ['name' => $participant->scout_name])}}
            </b-button>

        </form-quali-content>

    </b-card>

@endsection
