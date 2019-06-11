@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Qualikategorie bearbeiten')])

        @component('components.form', ['route' => ['admin.categories.update', ['course' => $course->id, 'qk' => $qk->id]]])

            @component('components.form.textInput', ['name' => 'name', 'label' => __('Titel'), 'required' => true, 'value' => $qk->name])@endcomponent

            @component('components.form.submit', ['label' => __('Speichern')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
