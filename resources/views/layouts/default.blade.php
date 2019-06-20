@extends('layouts.master')

@section('layout')

    @include('includes.header', ['navigation' => true])

    <div class="container" id="app" v-cloak>

        @include('includes.alerts')

        @yield('content')

    </div>

    @include('includes.footer')

@endsection
