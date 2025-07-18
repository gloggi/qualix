@extends('layouts.default')

@section('pagetitle'){{__('t.views.evaluation_grids.page_title', ['name' => $evaluationGridTemplate->name]) }}@endsection


@section('content')

    <b-card>
        <template #header>{{__('t.views.evaluation_grids.new', ['name' => $evaluationGridTemplate->name])}}</template>

        <form-basic :action="['evaluationGrid.store', { course: {{ $course->id }}, evaluation_grid_template: {{ $evaluationGridTemplate->id }} }]">

            <input-multi-select
                name="participants"
                model-value="{{ $participants }}"
                label="{{__('t.models.evaluation_grid.participants')}}"
                :options="{{ json_encode($course->participants->map->only('id', 'scout_name')) }}"
                :groups="{{json_encode(
                    $course->participantGroups->mapWithKeys(function ($group) {
                        return [$group['group_name'] => $group->participants->pluck('id')->join(',')];
                    }), JSON_FORCE_OBJECT)}}"
                display-field="scout_name"
                multiple
                required
                :autofocus="{{ $participants === null ? 'true' : 'false' }}"></input-multi-select>

            <input-multi-select
                name="block"
                model-value="{{ $block }}"
                label="{{__('t.models.evaluation_grid.block')}}"
                required
                :options="{{ json_encode($blocks->map->only('id', 'blockname_and_number')) }}"
                display-field="blockname_and_number"></input-multi-select>

            <input-evaluation-grid
                name="rows"
                :model-value="{{ json_encode($evaluationGridRows, JSON_FORCE_OBJECT) }}"
                :row-templates="{{ json_encode($evaluationGridTemplate->evaluationGridRowTemplates) }}"
                :notes-length-limit="{{ \App\Models\Observation::CHAR_LIMIT }}"></input-evaluation-grid>

            <button-submit></button-submit>

        </form-basic>

    </b-card>

@endsection
