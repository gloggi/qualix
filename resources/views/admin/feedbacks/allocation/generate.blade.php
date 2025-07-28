@extends('layouts.default')

@section('pagetitle'){{__('t.views.admin.participant_group_generator.page_title') }}@endsection

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.feedbacks.allocation.generate_allocation')}}</template>
        <form-feedback-allocation
            :action="['admin.feedbacks.allocate', { course: {{ $course->id }}, feedback_data: {{ $feedback_data->id }} }]"
            :update-assignment-action="['admin.feedbacks.assignments.update', { course: {{ $course->id }}, feedback_data: {{ $feedback_data->id }} }]"
            course-id="{{ $course->id }}"

            :participants="{{ json_encode($feedback_data->participants->map->only('id', 'scout_name')) }}"

            :trainers="{{ json_encode($course->users->map->only('id', 'name')) }}"
        >

        </form-feedback-allocation>

    </b-card>

@endsection
