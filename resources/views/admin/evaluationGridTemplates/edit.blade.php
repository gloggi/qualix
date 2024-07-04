@extends('layouts.default')

@section('pagetitle'){{__('t.views.admin.evaluation_grid_templates.page_title_edit') }}@endsection

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.evaluation_grid_templates.edit')}}</template>

        <form-evaluation-grid-template
            :action="['admin.evaluation_grid_templates.update', { course: {{ $course->id }}, evaluation_grid_template: {{ $evaluationGridTemplate->id }} }]"
            course-id="{{ $course->id }}"
            :requirements="{{ json_encode($course->requirements->map->only('id', 'content')) }}"
            :blocks="{{ json_encode($course->blocks->map->only('id', 'blockname_and_number')) }}"
            :control-types="{{ json_encode(\App\Models\EvaluationGridRowTemplate::CONTROL_TYPES) }}"
            :evaluation-grid-template="{{ json_encode($evaluationGridTemplate) }}">

            <template #submit>
                <button-submit>
                    <a href="{{ \Illuminate\Support\Facades\URL::route('admin.evaluation_grid_templates', ['course' => $course->id]) }}">{{__('t.views.admin.evaluation_grid_templates.go_back_to_evaluation_grid_template_list')}}</a>
                </button-submit>
            </template>

        </form-evaluation-grid-template>

    </b-card>

@endsection
