@extends('layouts.default')

@section('pagetitle'){{__('t.views.name_game.page_title') }}@endsection

@section('content')

    <b-card>
        <template #header>
            <div class="d-flex justify-content-between">
                {{__('t.views.name_game.name_game')}}
                <a href="{{ route('participants', [ 'course' => $course->id ]) }}">{{ __('t.views.name_game.abort') }}</a>
            </div>

        </template>

            <name-game
                :participants="{{ json_encode($course->participants) }}"
                :team-members="{{ json_encode($course->users) }}"
            ></name-game>

    </b-card>

@endsection
