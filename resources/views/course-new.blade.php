@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.new_course.title')}}</template>

        @component('components.form', ['route' => 'admin.newcourse.store'])

            @component('components.form.textInput', ['name' => 'name', 'label' => __('t.models.course.name'), 'required' => true, 'autofocus' => true])@endcomponent

            @component('components.form.textInput', ['name' => 'course_number', 'label' => __('t.models.course.course_number')])@endcomponent

            @component('components.form.submit', ['label' => __('t.views.admin.new_course.create')])@endcomponent

        @endcomponent

    </b-card>

@endsection
