@extends('layouts.default')

@section('wideLayout'){{ json_encode(!!$qualiData) }}@endsection

@section('content')

    <b-card>
        <template #header>{{__('t.views.overview.title')}}</template>

        @if (count($participants))

            @if($showQualis)
                <div class="d-flex justify-content-end mb-2">
                    <label for="user" class="col-form-label text-md-right mr-2">{{ __('t.views.overview.show_qualis') }}</label>
                    <multi-select
                        name="quali_data"
                        :value="{{ $qualiData ? json_encode("{$qualiData->id}") : json_encode("0") }}"
                        class=""
                        required
                        :options="{{ json_encode($qualiOptions) }}"
                        display-field="name"
                        @update:selected="selected => $window.location = routeUri('overview', {course: {{ $course->id }}, quali_data: selected.id || ''})"></multi-select>
                </div>
            @endif

            <table-observation-overview
                multiple
                :users="{{ json_encode($course->users) }}"
                :participants="{{ json_encode(collect($participants)->map->observationCountsByUser()) }}"
                :quali-data="{{ json_encode($qualiData) }}"
                :red-threshold="{{ json_encode($course->observation_count_red_threshold) }}"
                :green-threshold="{{ json_encode($course->observation_count_green_threshold) }}"></table-observation-overview>

        @else

            {{__('t.views.overview.no_participants', ['here' => $participantManagementLink])}}

        @endif

    </b-card>

@endsection
