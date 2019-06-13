@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('TN')])

        @component('components.form', ['route' => ['admin.participants.update', ['course' => $course->id, 'participant' => $participant->id]], 'enctype' => 'multipart/form-data'])

            @component('components.form.textInput', ['name' => 'scout_name', 'label' => 'Pfadiname', 'required' => true, 'autofocus' => true, 'value' => $participant->scout_name])@endcomponent

            @component('components.form.textInput', ['name' => 'group', 'label' => 'Abteilung', 'value' => $participant->group])@endcomponent

            @component('components.form.fileInput', ['name' => 'image', 'label' => 'Bild', 'accept' => 'image/*'])@endcomponent

            @component('components.form.submit', ['label' => __('Speichern')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
