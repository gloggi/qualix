@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Qualikategorie bearbeiten')])

        @component('components.form', ['route' => ['admin.qk.update', ['kurs' => $kurs->id, 'qk' => $qk->id]]])

            @component('components.form.textInput', ['name' => 'quali_kategorie', 'label' => __('Titel'), 'required' => true, 'value' => $qk->quali_kategorie])@endcomponent

            @component('components.form.submit', ['label' => __('Speichern')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
