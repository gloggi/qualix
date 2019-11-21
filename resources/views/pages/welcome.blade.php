@extends('layouts.default')

@section('content')

    @component('components.card')

        <h1>{{__('t.views.welcome.title')}}</h1>

        <p>{{__('t.views.welcome.text')}}</p>

    @endcomponent
@endsection
