@extends('layouts.default')

@section('pagetitle'){{__('t.views.page_titles.admin_observationAssignments') }}@endsection

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.observation_assignments.new')}}</template>

        <form-basic :action="['admin.observationAssignments.store', {course: {{ $course->id }}}]">
            <input-text name="name" label="{{__('t.models.observation_assignment.name')}}" required autofocus></input-text>

            <input-multi-select
                name="users"
                label="{{__('t.models.observation_assignment.users')}}"
                :options="{{ json_encode($course->users->map->only('id', 'name')) }}"
                display-field="name"
                multiple
                required
                :autofocus="true"></input-multi-select>

            <input-multi-select
                name="participants"
                label="{{__('t.models.observation_assignment.participants')}}"
                :options="{{ json_encode($course->participants->map->only('id', 'scout_name')) }}"
                :groups="{{json_encode(
                    $course->participantGroups->mapWithKeys(function ($group) {
                        return [$group['group_name'] => $group->participants->pluck('id')->join(',')];
                    }), JSON_FORCE_OBJECT)}}"
                display-field="scout_name"
                multiple
                required
                :autofocus="true"></input-multi-select>

            @php
                $days = $course->blocks->mapToGroups(function($block) {
                    return [$block->block_date->formatLocalized(__('t.global.date_format')) => $block->id];
                })->map(function($ids, $date) {
                    return implode(',', $ids->all());
                });
            @endphp

            <input-multi-select
                name="blocks"
                label="{{__('t.models.observation_assignment.blocks')}}"
                required
                :options="{{ json_encode($course->blocks->map->only('id', 'blockname_and_number')) }}"
                :groups="{{ json_encode($days, JSON_FORCE_OBJECT) }}"
                :autofocus="true"
                display-field="blockname_and_number"
                multiple
                :autofocus="true"
                ></input-multi-select>

            <button-submit label="{{__('t.global.add')}}">

                @component('components.help-text', ['id' => 'requirementsHelp', 'key' => 't.views.admin.observation_assignments.what_are_observation_assignments'])@endcomponent

            </button-submit>

        </form-basic>

    </b-card>

    <b-card>
        <template #header>{{__('t.views.admin.observation_assignments.existing', ['courseName' => $course->name])}}</template>

        @if (count($course->observationAssignments))

            <responsive-table
                :data="{{ json_encode($course->observationAssignments()->with('users', 'participants', 'blocks')->get()) }}"
                :fields="[
                    { label: $t('t.models.observation_assignment.name'), value: observationAssignment => observationAssignment.name },
                    { label: $t('t.models.observation_assignment.users'), value: observationAssignment => observationAssignment.users.map(user => user.name).join(', ') },
                    { label: $t('t.models.observation_assignment.participants'), value: observationAssignment => observationAssignment.participants.map(participant => participant.name_and_group).join(', ') },
                    { label: $t('t.models.observation_assignment.blocks'), value: observationAssignment => observationAssignment.blocks.map(block => block.blockname_and_number).join(', ') },
                ]"
                :actions="{
                    edit: observationAssignment => routeUri('admin.observationAssignments.edit', {course: {{ $course->id }}, observationAssignment: observationAssignment.id}),
                    delete: observationAssignment => ({
                        text: $t('t.views.admin.observation_assignments.really_delete', observationAssignment),
                        route: ['admin.observationAssignments.delete', {course: {{ $course->id }}, observationAssignment: observationAssignment.id}]
                    })
                }"></responsive-table>

        @else

            {{__('t.views.admin.observation_assignments.no_observation_assignment')}}

            @component('components.help-text', ['id' => 'noGroupsHelp', 'key' => 't.views.admin.observation_assignments.are_observation_assignments_required'])@endcomponent

        @endif

    </b-card>

@endsection
