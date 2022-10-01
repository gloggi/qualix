@extends('layouts.default')

@section('pagetitle'){{__('t.views.page_titles.admin_feedbacks') }}@endsection


@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.feedbacks.new')}}</template>

        <form-feedback-data
            :action="['admin.feedbacks.store', { course: {{ $course->id }} }]"
            course-id="{{ $course->id }}"
            :participants="{{ json_encode($course->participants->map->only('id', 'scout_name')) }}"
            :participant-groups="{{json_encode(
                $course->participantGroups->mapWithKeys(function ($group) {
                    return [$group['group_name'] => $group->participants->pluck('id')->join(',')];
                }), JSON_FORCE_OBJECT)}}"
            :requirements="{{ json_encode($course->requirements->map->only('id', 'content')) }}"
            :requirement-statuses="{{ json_encode($course->requirement_statuses) }}"
            :trainers="{{ json_encode($course->users->map->only('id', 'name')) }}"
            feedback-contents-template>

            <template #submit>
                <button-submit label="{{__('t.views.admin.feedbacks.create')}}">
                    @component('components.help-text', ['id' => 'feedbackHelp', 'key' => 't.views.admin.feedbacks.what_are_feedbacks'])@endcomponent
                </button-submit>
            </template>

        </form-feedback-data>

    </b-card>

    <b-card>
        <template #header>{{__('t.views.admin.feedbacks.existing', ['courseName' => $course->name])}}</template>

        @if (count($course->feedback_datas))

            <responsive-table
                :data="{{ json_encode($course->feedback_datas) }}"
                :fields="[
                    { label: $t('t.models.feedback.name'), value: feedbackData => feedbackData.name },
                ]"
                :actions="{
                    edit: feedbackData => routeUri('admin.feedbacks.edit', {course: {{ $course->id }}, feedback_data: feedbackData.id}),
                    delete: feedbackData => ({
                        text: $t('t.views.admin.feedbacks.really_delete', feedbackData),
                        route: ['admin.feedbacks.delete', {course: {{ $course->id }}, feedback_data: feedbackData.id}]
                    })
                }"></responsive-table>

        @else

            {{__('t.views.admin.feedbacks.no_feedbacks')}}

        @endif

    </b-card>

@endsection
