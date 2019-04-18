@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Einladung in :kursname', ['kursname' => $invitation->kurs->name])])

        {{__('Du bist schon in der Equipe von :kursname. Du kannst diese Einladung nicht annehmen.', ['kursname' => $invitation->kurs->name])}}

    @endcomponent

@endsection
