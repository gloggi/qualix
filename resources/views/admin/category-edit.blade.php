@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Kategorie bearbeiten')])

        @component('components.form', ['route' => ['admin.categories.update', ['course' => $course->id, 'category' => $category->id]]])

            @component('components.form.textInput', ['name' => 'name', 'label' => __('Titel'), 'required' => true, 'autofocus' => true, 'value' => $category->name])@endcomponent

            @component('components.form.submit', ['label' => __('Speichern')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
