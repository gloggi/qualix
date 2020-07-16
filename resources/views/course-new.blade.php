@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.new_course.title')}}</template>

        @component('components.form', ['route' => 'admin.newcourse.store'])

            <input-text @forminput('name') label="{{__('t.models.course.name')}}" required autofocus></input-text>

            <input-text @forminput('course_number') label="{{__('t.models.course.course_number')}}"></input-text>

            <button-submit label="{{__('t.views.admin.new_course.create')}}"></button-submit>

        @endcomponent

    </b-card>

@endsection
