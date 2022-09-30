@extends('layouts.default')

@section('content')

    <b-card>

        <h1>{{__('t.views.welcome.title')}}</h1>

        <p>{{__('t.views.welcome.text')}}</p>

        <p>{{__('t.views.welcome.changelog')}} <a href="https://github.com/gloggi/qualix/blob/master/CHANGELOG.md#changelog" target="_blank">{{__('t.views.welcome.here')}}</a></p>
    </b-card>

@endsection
