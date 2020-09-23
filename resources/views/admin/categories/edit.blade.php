@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.categories.edit')}}</template>

        <form-basic :action="['admin.categories.update', { course: {{ $course->id }}, category: {{ $category->id }} }]">

            <input-text name="name" value="{{ $category->name }}" label="{{__('t.models.category.name')}}" required autofocus></input-text>

            <button-submit></button-submit>

        </form-basic>

    </b-card>

@endsection
