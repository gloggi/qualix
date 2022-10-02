@extends('layouts.default')

@section('pagetitle'){{__('t.views.admin.observation_assignments.page_title_edit') }}@endsection

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.observation_assignments.edit')}}</template>

        <form-basic :action="['admin.observationAssignments.update', {course: {{ $course->id }}, observationAssignment: {{ $observationAssignment->id }}}]">

            <input-text name="name" value="{{ $observationAssignment->name }}" label="{{__('t.models.observation_assignment.name')}}" required autofocus></input-text>

            <input-multi-select
                name="users"
                value="{{ $observationAssignment->users->pluck('id')->join(',') }}"
                label="{{__('t.models.observation_assignment.users')}}"
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

            @php
                $days = $course->blocks->mapToGroups(function($block) {
                    return [$block->block_date->formatLocalized(__('t.global.date_format')) => $block->id];
                })->map(function($ids, $date) {
                    return implode(',', $ids->all());
                });
            @endphp

            <input-multi-select
                name="blocks"
                value="{{ $observationAssignment->blocks->pluck('id')->join(',') }}"
                label="{{__('t.models.observation_assignment.blocks')}}"
                required
                :options="{{ json_encode($course->blocks->map->only('id', 'blockname_and_number')) }}"
                :groups="{{ json_encode($days, JSON_FORCE_OBJECT) }}"
                display-field="blockname_and_number"
                multiple></input-multi-select>

            <button-submit></button-submit>

        </form-basic>

    </b-card>

@endsection
