@extends('layouts.default')

@section('pagetitle'){{__('t.views.observations.page_title') }}@endsection

@section('content')

    <b-card>
        <template #header>{{__('t.views.observations.new')}}</template>

        <form-observation
            :action="['observation.store', { course: {{ $course->id }} }]"
            participants="{{ $participants }}"
            :all-participants="{{ json_encode($course->participants->map->only('id', 'scout_name')) }}"
            :all-participant-groups="{{json_encode(
                $course->participantGroups->mapWithKeys(function ($group) {
                    return [$group['group_name'] => $group->participants->pluck('id')->join(',')];
                }), JSON_FORCE_OBJECT)}}"
            :autofocus-participants="{{ $participants === null ? 'true' : 'false' }}"
            :content-char-limit="{{ App\Models\Observation::CHAR_LIMIT }}"
            :block-requirements-mapping="{{ json_encode($course->blocks->map->only('id', 'requirement_ids')) }}"
            block="{{ $block }}"
            :all-blocks="{{ json_encode($blocks->map->only('id', 'blockname_and_number', 'requirement_ids')) }}"
            :course-id="{{ $course->id }}"
            :evaluation-grid-templates-mapping="{{ json_encode($course->evaluationGridTemplatesPerBlock()) }}"
            requirements="{{ old('requirements') }}"
            :all-requirements="{{ json_encode($course->requirements->map->only('id', 'content')) }}"
            :use-impressions="{{ $course->uses_impressions ? 'true' : 'false' }}"
            :all-categories="{{ json_encode($course->categories->map->only('id', 'name')) }}"
        ></form-observation>

    </b-card>

@endsection

