@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('t.views.admin.categories.edit')])

        @component('components.form', ['route' => ['admin.categories.update', ['course' => $course->id, 'category' => $category->id]]])

            @component('components.form.textInput', ['name' => 'name', 'label' => __('t.models.category.name'), 'required' => true, 'autofocus' => true, 'value' => $category->name])@endcomponent

            @component('components.form.submit', ['label' => __('t.global.save')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
