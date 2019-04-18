@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('User bearbeiten')])

        @component('components.form', ['route' => ['user.update'], 'enctype' => 'multipart/form-data'])

            @component('components.form.textInput', ['name' => 'name', 'label' => 'Name', 'required' => true, 'value' => $user->name])@endcomponent

            @component('components.form.textInput', ['name' => 'abteilung', 'label' => 'Abteilung', 'value' => $user->abteilung])@endcomponent

            @component('components.form.fileInput', ['name' => 'bild', 'label' => 'Bild', 'accept' => 'image/*'])@endcomponent

            @component('components.form.submit', ['label' => __('Speichern')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
