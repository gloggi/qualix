@extends('layouts.print')

@section('pagetitle'){{ $feedback->name }} {{ $participant->scout_name }}@endsection

@section('content')

    @component('feedbackContent.header', ['feedback' => $feedback, 'participant' => $participant, 'course' => $course])
        @if($feedback->requirements()->count())
            <div class="mt-2">
                <h5>{{__('t.views.feedback_content.requirements_status')}}</h5>
                <requirement-progress :requirements="{{ json_encode($feedback->requirements) }}" :statuses="{{ json_encode($course->requirement_statuses) }}"></requirement-progress>
            </div>
        @endif
    @endcomponent

    <feedback-editor
        readonly
        name=""
        course-id="{{ $course->id }}"
        :value="{{ json_encode($feedback->contents) }}"
        :observations="{{ json_encode($observations) }}"
        :requirements="{{ json_encode($feedback->requirements) }}"
        :requirement-statuses="{{ json_encode($course->requirement_statuses) }}"
        @content-ready="() => $window.PagedPolyfill.preview()"></feedback-editor>

@endsection
