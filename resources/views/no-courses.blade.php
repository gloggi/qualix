@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('t.views.welcome.title')])

        {{ __('t.views.welcome.no_courses') }}

    @endcomponent

@endsection
