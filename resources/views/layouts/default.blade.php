@extends('layouts.master')

@section('pagetitle'){{__('t.global.page_title')}}@endsection

@section('head')
    <link type="text/css" rel="stylesheet" href="{{ mix('css/app.css') }}">
@endsection

@section('layout')

    <b-container id="app" v-cloak>

        @include('includes.header', ['navigation' => true])

        @include('includes.alerts')

        @yield('content')

        @include('includes.footer')

    </b-container>

@endsection
