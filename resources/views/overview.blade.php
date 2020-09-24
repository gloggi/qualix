@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.overview.title')}}</template>

        @if (count($participants))

            <table-observation-overview
                multiple
                :users="{{ json_encode($course->users) }}"
                :participants="{{ json_encode(collect($participants)->map->observationCountsByUser()) }}"></table-observation-overview>

        @else

            {{__('t.views.overview.no_participants', ['here' => $participantManagementLink])}}

        @endif

    </b-card>

@endsection
