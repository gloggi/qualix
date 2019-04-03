@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Kursdetails')])

        @component('components.form', ['route' => 'admin.kurs.update'])

            @component('components.form.textInput', ['name' => 'name', 'label' => 'Kursname', 'required' => true, 'value' => $kurs->name])@endcomponent

            @component('components.form.textInput', ['name' => 'kursnummer', 'label' => 'Kursnummer', 'value' => $kurs->kursnummer])@endcomponent

            @component('components.form.submit', ['label' => 'Speichern'])@endcomponent

        @endcomponent

    @endcomponent

@endsection
