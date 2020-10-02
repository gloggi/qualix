@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.observation_assignments.new')}}</template>

        <form-basic :action="['admin.observationAssignments.store', {course: {{ $course->id }}}]">
            <input-text name="order_name" label="{{__('t.models.observation_assignment.order_name')}}" required autofocus></input-text>

            <input-multi-select
                name="user"
                label="{{__('t.models.observation_assignment.user')}}"
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

            <input-multi-select
                name="block"
                label="{{__('t.models.observation_assignment.block')}}"
                required
                :options="{{ json_encode($course->blocks->map->only('id', 'blockname_and_number')) }}"
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
                    { label: $t('t.models.observation_assignment.order_name'), value: observationAssignment => observationAssignment.order_name },
                    { label: $t('t.models.observation_assignment.user'), value: observationAssignment => observationAssignment.users.map(user => user.name).join(', ') },
                    { label: $t('t.models.observation_assignment.participants'), value: observationAssignment => observationAssignment.participants.map(participant => participant.name_and_group).join(', ') },
                    { label: $t('t.models.observation_assignment.block'), value: observationAssignment => observationAssignment.blocks.map(block => block.blockname_and_number).join(', ') },
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
