@extends('layouts.master')

@section('layout')

    @include('includes.header', ['navigation' => true])

    <div class="container">
        @yield('content')
    </div>

    @include('includes.footer')

@endsection
