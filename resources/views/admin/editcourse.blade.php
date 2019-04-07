@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Kursdetails :courseName', ['courseName' => $kurs->name])])

        @component('components.form', ['route' => ['admin.kurs.update', ['kurs' => $kurs->id]]])

            @component('components.form.textInput', ['name' => 'name', 'label' => __('Kursname'), 'required' => true, 'value' => $kurs->name])@endcomponent

            @component('components.form.textInput', ['name' => 'kursnummer', 'label' => __('Kursnummer'), 'value' => $kurs->kursnummer])@endcomponent

            @component('components.form.submit', ['label' => __('Speichern')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
