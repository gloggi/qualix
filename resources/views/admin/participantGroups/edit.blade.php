@extends('layouts.default')

@section('pagetitle'){{__('t.views.admin.participant_groups.page_title_edit') }}@endsection

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.participant_groups.edit')}}</template>

        <form-basic :action="['admin.participantGroups.update', { course: {{ $course->id }}, participantGroup: {{ $participantGroup->id }} }]">

            <input-text name="group_name" model-value="{{ $participantGroup->group_name }}" label="{{__('t.models.participant_group.group_name')}}" required autofocus></input-text>

            <input-multi-select
                name="participants"
                model-value="{{ $participantGroup->participants->pluck('id')->join(',') }}"
                label="{{__('t.models.participant_group.participants')}}"
                required
                :options="{{ json_encode($course->participants->map->only('id', 'scout_name')) }}"
                display-field="scout_name"
                multiple></input-multi-select>

            <button-submit></button-submit>

        </form-basic>

    </b-card>

@endsection
