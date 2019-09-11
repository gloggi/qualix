@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Willkommen bei Qualix!')])

        {{ __('Du bist momentan noch in keinem Kurs eingetragen. Lass dich in einen Kurs einladen oder erstelle selber einen neuen.') }}

    @endcomponent

@endsection
