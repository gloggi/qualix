@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Einladung in :coursename', ['coursename' => $invitation->course->name])])

        {{__('Du bist schon in der Equipe von :coursename. Du kannst diese Einladung nicht annehmen.', ['coursename' => $invitation->course->name])}}

    @endcomponent

@endsection
