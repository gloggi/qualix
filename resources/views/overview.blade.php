@extends('layouts.default')

@section('wideLayout'){{ json_encode(!!$feedbackData) }}@endsection

@section('pagetitle'){{__('t.views.page_titles.overview')}}@endsection

@section('content')

    <b-card>
        <template #header>{{__('t.views.overview.title')}}</template>

        @if (count($participants))

            @if($showFeedbacks)
                <div class="d-flex justify-content-end mb-2">
                    <label for="user" class="col-form-label text-md-right mr-2">{{ __('t.views.overview.show_feedbacks') }}</label>
                    <multi-select
                        name="feedback_data"
                        :value="{{ $feedbackData ? json_encode("{$feedbackData->id}") : json_encode("0") }}"
                        class=""
                        required
                        :options="{{ json_encode($feedbackOptions) }}"
                        display-field="name"
                        @update:selected="selected => $window.location = routeUri('overview', {course: {{ $course->id }}, feedback_data: selected.id || ''})"></multi-select>
                </div>
            @endif

            <table-observation-overview
                multiple
                :users="{{ json_encode($course->users) }}"
                :participants="{{ json_encode(collect($participants)->map->observationCountsByUser()) }}"
                :feedback-data="{{ json_encode($feedbackData) }}"
                :requirement-statuses="{{ json_encode($course->requirement_statuses) }}"
                :red-threshold="{{ json_encode($course->observation_count_red_threshold) }}"
                :green-threshold="{{ json_encode($course->observation_count_green_threshold) }}"></table-observation-overview>

        @else

            {{__('t.views.overview.no_participants', ['here' => $participantManagementLink])}}

        @endif

    </b-card>

@endsection
