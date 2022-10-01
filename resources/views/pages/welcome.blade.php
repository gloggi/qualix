@extends('layouts.default')

@section('content')

    <b-card>

        <h1>{{__('t.views.welcome.title')}}</h1>

        <p>{{__('t.views.welcome.text')}}</p>

        <p>{{__('t.views.welcome.changelog', ['here' => $changeLogLink])}}</p>

    </b-card>

@endsection
