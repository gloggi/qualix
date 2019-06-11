@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Mindestanforderung bearbeiten')])

        @component('components.form', ['route' => ['admin.requirements.update', ['course' => $course->id, 'requirement' => $requirement->id]]])

            @component('components.form.textInput', ['name' => 'content', 'label' => __('Anforderung'), 'required' => true, 'value' => $requirement->content])@endcomponent

            @component('components.form.checkboxInput', ['name' => 'mandatory', 'label' => __('Killer-Kriterium'), 'value' => $requirement->mandatory])@endcomponent

            @component('components.form.submit', ['label' => __('Speichern')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
