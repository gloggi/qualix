@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.participant_groups.edit')}}</template>

        @component('components.form', ['route' => ['admin.participantGroups.update', ['course' => $course->id, 'participantGroup' => $participantGroup->id]]])

            <input-text @forminput('group_name', $participantGroup->group_name) label="{{__('t.models.participant_group.group_name')}}" required autofocus></input-text>


            <input-multi-select
                @forminput('participants', $participantGroup->participants->pluck('id')->join(','))
                label="{{__('t.models.participant_group.participants')}}"
                required
                :options="{{ json_encode($course->participants->map->only('id', 'scout_name')) }}"
                display-field="scout_name"
                multiple></input-multi-select>

            <button-submit></button-submit>

        @endcomponent

    </b-card>

@endsection
