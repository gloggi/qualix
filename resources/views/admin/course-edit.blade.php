@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Kursdetails :courseName', ['courseName' => $course->name])])

        @component('components.form', ['route' => ['admin.course.update', ['course' => $course->id]]])

            @component('components.form.textInput', ['name' => 'name', 'label' => __('Kursname'), 'required' => true, 'autofocus' => true, 'value' => $course->name])@endcomponent

            @component('components.form.textInput', ['name' => 'course_number', 'label' => __('Kursnummer'), 'value' => $course->course_number])@endcomponent

            @component('components.form.submit', ['label' => __('Speichern')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
