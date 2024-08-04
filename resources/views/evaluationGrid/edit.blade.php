@extends('layouts.default')

@section('pagetitle'){{__('t.views.evaluation_grids.page_title_edit', ['name' => $evaluationGridTemplate->name]) }}@endsection

@section('content')

    <b-card>
        <template #header>{{__('t.views.evaluation_grids.edit', ['name' => $evaluationGridTemplate->name])}}</template>

        <form-basic :action="['evaluationGrid.update', { course: {{ $course->id }}, evaluation_grid_template: {{ $evaluationGridTemplate->id }}, evaluation_grid: {{ $evaluationGrid->id }} }]">

            <input-multi-select
                name="participants"
                value="{{ $evaluationGrid->participants->pluck('id')->join(',') }}"
                label="{{__('t.models.evaluation_grid.participants')}}"
                required
                :options="{{ json_encode($course->participants->map->only('id', 'scout_name')) }}"
                :groups="{{json_encode(
                        $course->participantGroups->mapWithKeys(function ($group) {
                            return [$group['group_name'] => $group->participants->pluck('id')->join(',')];
                        }), JSON_FORCE_OBJECT)}}"
                display-field="scout_name"
                multiple></input-multi-select>

            <input-multi-select
                name="block"
                value="{{ $evaluationGrid->block->id }}"
                label="{{__('t.models.evaluation_grid.block')}}"
                required
                :options="{{ json_encode($course->blocks->map->only('id', 'blockname_and_number')) }}"
                display-field="blockname_and_number"></input-multi-select>

            <input-evaluation-grid
                name="rows"
                :value="{{ json_encode($evaluationGrid->rows->values()->keyBy('evaluation_grid_row_template_id'), JSON_FORCE_OBJECT) }}"
                :row-templates="{{ json_encode($evaluationGridTemplate->evaluationGridRowTemplates) }}"
                :notes-length-limit="{{ \App\Models\Observation::CHAR_LIMIT }}"></input-evaluation-grid>

            <button-submit></button-submit>

        </form-basic>

    </b-card>

@endsection
