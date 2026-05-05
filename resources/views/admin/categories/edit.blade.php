@extends('layouts.default')

@section('pagetitle'){{__('t.views.admin.categories.page_title_edit') }}@endsection

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.categories.edit')}}</template>

        <form-basic :action="['admin.categories.update', { course: {{ $course->id }}, category: {{ $category->id }} }]">

            <input-text name="name" model-value="{{ $category->name }}" label="{{__('t.models.category.name')}}" required autofocus></input-text>

            <button-submit></button-submit>

        </form-basic>

    </b-card>

@endsection
