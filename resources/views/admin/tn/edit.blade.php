@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('TN')])

        @component('components.form', ['route' => ['admin.tn.update', ['kurs' => $kurs->id, 'tn' => $tn->id]], 'enctype' => 'multipart/form-data'])

            @component('components.form.textInput', ['name' => 'pfadiname', 'label' => 'Pfadiname', 'required' => true, 'value' => $tn->pfadiname])@endcomponent

            @component('components.form.textInput', ['name' => 'abteilung', 'label' => 'Abteilung', 'value' => $tn->abteilung])@endcomponent

            @component('components.form.fileInput', ['name' => 'bild', 'label' => 'Bild', 'accept' => 'image/*'])@endcomponent

            @component('components.form.submit', ['label' => __('Speichern')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
