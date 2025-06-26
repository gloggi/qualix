@extends('layouts.master')

@section('wideLayout'){{ json_encode(false) }}@endsection

@section('head')
    @vite('resources/sass/app.scss')
@endsection

@section('layout')

    <div id="app" v-cloak>
        <b-container>

            @include('includes.header', ['navigation' => true])

            @include('includes.alerts')

        </b-container>

        <b-container :fluid="@yield('wideLayout')">

            @yield('content')

        </b-container>

        <b-container>

            @include('includes.footer')

        </b-container>
    </div>

@endsection
