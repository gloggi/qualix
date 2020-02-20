@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('t.views.admin.requirements.edit')])

        @component('components.form', ['route' => ['admin.requirements.update', ['course' => $course->id, 'requirement' => $requirement->id]]])

            @component('components.form.textInput', ['name' => 'content', 'label' => __('t.models.requirement.content'), 'required' => true, 'autofocus' => true, 'value' => $requirement->content])@endcomponent

            @component('components.form.checkboxInput', ['name' => 'mandatory', 'label' => __('t.models.requirement.mandatory'), 'value' => $requirement->mandatory])@endcomponent

            @component('components.form.submit', ['label' => __('t.global.save')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
