@extends('layouts.default')

@section('wideLayout'){{ json_encode(!!$feedbackData || $evaluationGridTemplates->count()) }}@endsection

@section('pagetitle'){{__('t.views.overview.page_title') }}@endsection

@section('content')

    <b-card>
        <template #header>{{__('t.views.overview.title')}}</template>

        @if (count($participants))

            @if($showFeedbacks || $showEvaluationGridTemplates)
                <div class="d-flex flex-wrap gap-1rem justify-content-end">
                    @if($showEvaluationGridTemplates)
                        <div class="d-flex flex-wrap gap-1rem mb-2">
                            <label for="evaluation_grid_template" class="col-form-label text-md-end ms-2">{{ __('t.views.overview.show_evaluation_grids') }}</label>
                            <multi-select
                                name="evaluation_grid_template"
                                :value="{{ $evaluationGridTemplates->count() ? json_encode("{$evaluationGridTemplates->map->id->join(',')}") : json_encode("0") }}"
                                class=""
                                multiple
                                placeholder="{{__('t.views.overview.no_evaluation_grid')}}"
                                :options="{{ json_encode($evaluationGridTemplateOptions) }}"
                                :groups="{{ json_encode($evaluationGridTemplateGroups) }}"
                                display-field="name"
                                @update:selected="selected => { $window.location = routeUri('overview', {course: {{ $course->id }}, {{ $feedbackData ? "feedback_data: {$feedbackData->id}," : '' }} evaluation_grid_templates: selected.map(e => e.id).join(',')}) }"></multi-select>
                        </div>
                    @endif
                    @if($showFeedbacks)
                        <div class="d-flex flex-wrap gap-1rem mb-2">
                            <label for="feedback_data" class="col-form-label text-md-end ms-2">{{ __('t.views.overview.show_feedbacks') }}</label>
                            <multi-select
                                name="feedback_data"
                                :value="{{ $feedbackData ? json_encode("{$feedbackData->id}") : json_encode("0") }}"
                                class=""
                                :options="{{ json_encode($feedbackOptions) }}"
                                display-field="name"
                                @update:selected="selected => $window.location = routeUri('overview', {course: {{ $course->id }}, feedback_data: selected.id || '', {{ $evaluationGridTemplates->count() ? "evaluation_grid_templates: '{$evaluationGridTemplates->map->id->join(',')}'," : '' }} })"></multi-select>
                        </div>
                    @endif
                </div>
            @endif

            <table-observation-overview
                multiple
                :users="{{ json_encode($course->users) }}"
                :participants="{{ json_encode(collect($participants)->map->observationCountsByUser()) }}"
                :feedback-data="{{ json_encode($feedbackData) }}"
                :requirement-statuses="{{ json_encode($course->requirement_statuses) }}"
                :evaluation-grid-templates="{{ json_encode($evaluationGridTemplates) }}"
                :red-threshold="{{ json_encode($course->observation_count_red_threshold) }}"
                :green-threshold="{{ json_encode($course->observation_count_green_threshold) }}"></table-observation-overview>

        @else

            {{__('t.views.overview.no_participants', ['here' => $participantManagementLink])}}

        @endif

    </b-card>

@endsection
