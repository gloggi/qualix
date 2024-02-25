@extends('layouts.default')

@section('pagetitle'){{__('t.views.name_game.page_title') }}@endsection

@section('content')

    <b-card>
        <template #header>{{__('t.views.name_game.name_game')}}</template>

            <name-game
                :participants="{{ json_encode($course->participants) }}"
                :team-members="{{ json_encode($course->users) }}"
            ></name-game>

    </b-card>

@endsection
