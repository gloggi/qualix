@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Neuen Kurs erstellen')])

        @component('components.form', ['route' => 'admin.neuerkurs.store'])

            @component('components.form.textInput', ['name' => 'name', 'label' => 'Kursname', 'required' => true])@endcomponent

            @component('components.form.textInput', ['name' => 'kursnummer', 'label' => 'Kursnummer'])@endcomponent

            @component('components.form.submit', ['label' => 'Kurs eröffnen'])@endcomponent

        @endcomponent

    @endcomponent

@endsection
