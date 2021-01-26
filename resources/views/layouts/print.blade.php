@extends('layouts.master')

@section('head')
    <link type="text/css" rel="stylesheet" href="{{ mix('css/print.css') }}">
    <script src="{{ mix('js/print.js') }}"></script>
@endsection

@section('layout')

    <b-container id="app" v-cloak class="print">
        @yield('content')
    </b-container>

@endsection
