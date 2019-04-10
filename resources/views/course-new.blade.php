@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Neuen Kurs erstellen')])

        @component('components.form', ['route' => 'admin.neuerkurs.store'])

            @component('components.form.textInput', ['name' => 'name', 'label' => __('Kursname'), 'required' => true])@endcomponent

            @component('components.form.textInput', ['name' => 'kursnummer', 'label' => __('Kursnummer')])@endcomponent

            @component('components.form.submit', ['label' => __('Kurs eröffnen')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
