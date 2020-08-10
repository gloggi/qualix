@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.participants.edit')}}</template>

        @component('components.form', ['route' => ['admin.participants.update', ['course' => $course->id, 'participant' => $participant->id]], 'enctype' => 'multipart/form-data'])

            <input-text name="scout_name" value="{{ $participant->scout_name }}" label="{{__('t.models.participant.scout_name')}}" required autofocus></input-text>

            <input-text name="group" value="{{ $participant->group }}" label="{{__('t.models.participant.group')}}"></input-text>

            <input-file name="image" label="{{__('t.models.participant.image')}}" accept="image/*"></input-file>

            <button-submit></button-submit>

        @endcomponent

    </b-card>

@endsection
