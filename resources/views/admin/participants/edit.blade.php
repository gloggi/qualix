@extends('layouts.default')

@section('pagetitle'){{__('t.views.page_titles.edit_participant', ['participantName' => $participant->scout_name]) }}@endsection


@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.participants.edit')}}</template>

        <form-basic :action="['admin.participants.update', { course: {{ $course->id }}, participant: {{ $participant->id }} }]" enctype="multipart/form-data">

            <input-text name="scout_name" value="{{ $participant->scout_name }}" label="{{__('t.models.participant.scout_name')}}" required autofocus></input-text>

            <input-text name="group" value="{{ $participant->group }}" label="{{__('t.models.participant.group')}}"></input-text>

            <input-file name="image" label="{{__('t.models.participant.image')}}" accept="image/*"></input-file>

            <input-textarea name="freetext" value="{{ $participant->freetext }}" label="{{__('t.models.participant.freetext')}}"></input-textarea>

            <button-submit></button-submit>

        </form-basic>

    </b-card>

@endsection
