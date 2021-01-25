@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.overview.title')}}</template>

        @if (count($participants))

            <table-observation-overview
                multiple
                :users="{{ json_encode($course->users) }}"
                :participants="{{ json_encode(collect($participants)->map->observationCountsByUser()) }}"
                :red-threshold="{{ json_encode($course->observation_count_red_threshold) }}"
                :green-threshold="{{ json_encode($course->observation_count_green_threshold) }}"></table-observation-overview>

        @else

            {{__('t.views.overview.no_participants', ['here' => $participantManagementLink])}}

        @endif

    </b-card>

@endsection
