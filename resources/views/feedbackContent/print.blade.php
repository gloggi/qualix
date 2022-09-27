@extends('layouts.master')

@section('pagetitle'){{ $feedback->name }} {{ $participant->scout_name }}@endsection

@section('head')
    <link type="text/css" rel="stylesheet" href="{{ mix('css/print.css') }}">
@endsection

@section('layout')

    <div id="app" v-cloak>
        <print-feedback
            :course="{{ json_encode($course) }}"
            :feedback="{{ json_encode($feedback) }}"
            :feedback-contents="{{ json_encode($feedback->contents) }}"
            :participant="{{ json_encode($participant) }}"
            :observations="{{ json_encode($observations) }}"
            :statuses="{{ json_encode($course->requirement_statuses) }}"
        />
    </div>

@endsection
