@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.participants.edit')}}</template>

        @component('components.form', ['route' => ['admin.participants.update', ['course' => $course->id, 'participant' => $participant->id]], 'enctype' => 'multipart/form-data'])

            @component('components.form.textInput', ['name' => 'scout_name', 'label' => __('t.models.participant.scout_name'), 'required' => true, 'autofocus' => true, 'value' => $participant->scout_name])@endcomponent

            @component('components.form.textInput', ['name' => 'group', 'label' => __('t.models.participant.group'), 'value' => $participant->group])@endcomponent

            @component('components.form.fileInput', ['name' => 'image', 'label' => __('t.models.participant.image'), 'accept' => 'image/*'])@endcomponent

            @component('components.form.submit', ['label' => __('t.global.save')])@endcomponent

        @endcomponent

    </b-card>

@endsection
