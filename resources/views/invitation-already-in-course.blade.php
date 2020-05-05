@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.invitation.title', ['courseName' => $invitation->course->name])}}</template>

        {{__('t.views.invitation.already_in_equipe', ['courseName' => $invitation->course->name])}}

    </b-card>

@endsection
