@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.categories.edit')}}</template>

        @component('components.form', ['route' => ['admin.categories.update', ['course' => $course->id, 'category' => $category->id]]])

            <input-text name="name" value="{{ $category->name }}" label="{{__('t.models.category.name')}}" required autofocus></input-text>

            <button-submit></button-submit>

        @endcomponent

    </b-card>

@endsection
