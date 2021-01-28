@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.new_course.title')}}</template>

        <form-basic action="admin.newcourse.store">

            <input-text name="name" label="{{__('t.models.course.name')}}" required autofocus></input-text>

            <input-text name="course_number" label="{{__('t.models.course.course_number')}}"></input-text>

            <input-hidden name="observation_count_red_threshold" value="5"></input-hidden>

            <input-hidden name="observation_count_green_threshold" value="10"></input-hidden>

            <button-submit label="{{__('t.views.admin.new_course.create')}}"></button-submit>

        </form-basic>

    </b-card>

@endsection
