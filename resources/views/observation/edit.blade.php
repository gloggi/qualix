@extends('layouts.default')

@section('pagetitle'){{__('t.views.observations.page_title_edit') }}@endsection

@section('content')

    <b-card>
        <template #header>{{__('t.views.observations.edit')}}</template>

        <form-observation
            :action="['observation.update', { course: {{ $course->id }}, observation: {{ $observation->id }} }]"
            participants="{{ $observation->participants->pluck('id')->join(',') }}"
            :all-participants="{{ json_encode($course->participants->map->only('id', 'scout_name')) }}"
            :all-participant-groups="{{json_encode(
                $course->participantGroups->mapWithKeys(function ($group) {
                    return [$group['group_name'] => $group->participants->pluck('id')->join(',')];
                }), JSON_FORCE_OBJECT)}}"
            content="{{ $observation->content }}"
            :content-char-limit="{{ App\Models\Observation::CHAR_LIMIT }}"
            block="{{ $observation->block->id }}"
            :all-blocks="{{ json_encode($course->blocks->map->only('id', 'blockname_and_number', 'requirement_ids')) }}"
            :course-id="{{ $course->id }}"
            requirements="{{ $observation->requirements->pluck('id')->join(',') }}"
            :all-requirements="{{ json_encode($course->requirements->map->only('id', 'content')) }}"
            :use-impressions="{{ $course->uses_impressions ? 'true' : 'false' }}"
            impression="{{ $observation->impression }}"
            categories="{{ $observation->categories->pluck('id')->join(',') }}"
            :all-categories="{{ json_encode($course->categories->map->only('id', 'name')) }}"
        ></form-observation>

    </b-card>

@endsection
