@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.feedbacks.edit')}}</template>

        <form-feedback-data
            :action="['admin.feedbacks.update', { course: {{ $course->id }}, feedback_data: {{ $feedback_data->id }} }]"
            course-id="{{ $course->id }}"
            :name="{{ json_encode($feedback_data->name) }}"
            :feedbacks="{{ json_encode($feedback_data->feedbacks) }}"
            :participants="{{ json_encode($course->participants->map->only('id', 'scout_name')) }}"
            :participant-groups="{{json_encode(
                $course->participantGroups->mapWithKeys(function ($group) {
                    return [$group['group_name'] => $group->participants->pluck('id')->join(',')];
                }), JSON_FORCE_OBJECT)}}"
            :requirements="{{ json_encode($course->requirements->map->only('id', 'content')) }}"
            :requirement-statuses="{{ json_encode($course->requirement_statuses) }}"
            :trainers="{{ json_encode($course->users->map->only('id', 'name')) }}">

            <template #submit>
                <button-submit>
                    <a href="{{ \Illuminate\Support\Facades\URL::route('admin.feedbacks', ['course' => $course->id]) }}">{{__('t.views.admin.feedbacks.go_back_to_feedback_list')}}</a>
                </button-submit>
            </template>

        </form-feedback-data>

    </b-card>

@endsection
