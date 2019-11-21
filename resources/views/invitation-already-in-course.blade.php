@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('t.views.invitation.title', ['courseName' => $invitation->course->name])])

        {{__('t.views.invitation.already_in_equipe', ['courseName' => $invitation->course->name])}}

    @endcomponent

@endsection
