@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.observation_assignments.edit')}}</template>

        <form-basic :action="['admin.observationAssignments.update', {course: {{ $course->id }}, observationAssignment: {{ $observationAssignment->id }}}]">

            <input-text name="order_name" value="{{ $observationAssignment->order_name }}" label="{{__('t.models.observation_assignment.order_name')}}" required autofocus></input-text>

            <input-multi-select
                name="user"
                value="{{ $observationAssignment->users->pluck('id')->join(',') }}"
                label="{{__('t.models.observation_assignment.user')}}"
                required
                :options="{{ json_encode($course->users->map->only('id', 'name')) }}"
                display-field="name"
                multiple></input-multi-select>

            <input-multi-select
                name="participants"
                value="{{ $observationAssignment->participants->pluck('id')->join(',') }}"
                label="{{__('t.models.observation_assignment.participants')}}"
                required
                :options="{{ json_encode($course->participants->map->only('id', 'scout_name')) }}"
                :groups="{{json_encode(
                    $course->participantGroups->mapWithKeys(function ($group) {
                        return [$group['group_name'] => $group->participants->pluck('id')->join(',')];
                    }), JSON_FORCE_OBJECT)}}"
                display-field="scout_name"
                multiple></input-multi-select>

            <input-multi-select
                name="block"
                value="{{ $observationAssignment->blocks->pluck('id')->join(',') }}"
                label="{{__('t.models.observation_assignment.block')}}"
                required
                :options="{{ json_encode($course->blocks->map->only('id', 'blockname_and_number')) }}"
                display-field="blockname_and_number"
                multiple></input-multi-select>

            <button-submit></button-submit>

        </form-basic>

    </b-card>

@endsection
