@extends('layouts.default')

@section('pagetitle'){{__('t.views.admin.evaluation_grid_templates.page_title') }}@endsection


@section('content')

    @if(!$course->archived)
        <b-card>
            <template #header>{{__('t.views.admin.evaluation_grid_templates.new')}}</template>

            <form-evaluation-grid-template
                :action="['admin.evaluation_grid_templates.store', { course: {{ $course->id }} }]"
                course-id="{{ $course->id }}"
                :requirements="{{ json_encode($course->requirements->map->only('id', 'content')) }}"
                :blocks="{{ json_encode($course->blocks->map->only('id', 'blockname_and_number')) }}"
                :control-types="{{ json_encode(\App\Models\EvaluationGridRowTemplate::CONTROL_TYPES) }}">

                <template #submit>
                    <button-submit label="{{__('t.views.admin.evaluation_grid_templates.create')}}">
                        @component('components.help-text', ['key' => 't.views.admin.evaluation_grid_templates.what_are_evaluation_grids', 'id' => 'evaluationGridHelp'])@endcomponent
                    </button-submit>
                </template>

            </form-evaluation-grid-template>

        </b-card>
    @endif

    <b-card>
        <template #header>{{__('t.views.admin.evaluation_grid_templates.existing', ['courseName' => $course->name])}}</template>

        @if (count($course->evaluation_grid_templates))

            <responsive-table
                :data="{{ json_encode($course->evaluation_grid_templates) }}"
                :fields="[
                    { label: $t('t.models.evaluation_grid_template.name'), value: evaluationGridTemplate => evaluationGridTemplate.name },
                ]"
                :actions="{
                    edit: evaluationGridTemplate => routeUri('admin.evaluation_grid_templates.edit', {course: {{ $course->id }}, evaluation_grid_template: evaluationGridTemplate.id}),
                    print: evaluationGridTemplate => ['button-print-evaluation-grid', { courseId: {{ $course->id }}, evaluationGridTemplateId: evaluationGridTemplate.id }],
                    delete: evaluationGridTemplate => ({
                        text: $t('t.views.admin.evaluation_grid_templates.really_delete', evaluationGridTemplate),
                        route: ['admin.evaluation_grid_templates.delete', {course: {{ $course->id }}, evaluation_grid_template: evaluationGridTemplate.id}]
                    })
                }"></responsive-table>

        @else

            {{__('t.views.admin.evaluation_grid_templates.no_evaluation_grid_templates')}}

        @endif

    </b-card>

@endsection
