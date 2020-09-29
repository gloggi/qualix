@extends('layouts.master')

@section('layout')

    <b-container id="app" v-cloak class="print">
        @yield('content')
    </b-container>

@endsection
