@extends('layouts.default')

@section('pagetitle'){{__('t.views.page_titles.admin_participant_groups') }}@endsection

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.participant_groups.new')}}</template>

        <form-basic :action="['admin.participantGroups.store', { course: {{ $course->id }} }]">

            <input-text name="group_name" label="{{__('t.models.participant_group.group_name')}}" required autofocus></input-text>

            <input-multi-select
                name="participants"
                label="{{__('t.models.participant_group.participants')}}"
                :options="{{ json_encode($course->participants->map->only('id', 'scout_name')) }}"
                display-field="scout_name"
                required
                multiple></input-multi-select>

            <button-submit label="{{__('t.global.add')}}">

                @component('components.help-text', ['id' => 'requirementsHelp', 'key' => 't.views.admin.participant_groups.what_are_participant_groups'])@endcomponent

            </button-submit>

        </form-basic>

    </b-card>

    <b-card>
        <template #header>{{__('t.views.admin.participant_groups.existing', ['courseName' => $course->name])}}</template>

        @if (count($course->participantGroups))

            <responsive-table
                :data="{{ json_encode($course->participantGroups) }}"
                :fields="[
                    { label: $t('t.models.participant_group.group_name'), value: participantGroup => participantGroup.group_name },
                    { label: $t('t.models.participant_group.participants'), value: participantGroup => participantGroup.participant_names },
                ]"
                :actions="{
                    edit: participantGroup => routeUri('admin.participantGroups.edit', {course: {{ $course->id }}, participantGroup: participantGroup.id}),
                    delete: participantGroup => ({
                        text: $t('t.views.admin.participant_groups.really_delete', participantGroup),
                        route: ['admin.participantGroups.delete', {course: {{ $course->id }}, participantGroup: participantGroup.id}]
                    })
                }"
            ></responsive-table>

        @else

            {{__('t.views.admin.participant_groups.no_participant_group')}}

            @component('components.help-text', ['id' => 'noGroupsHelp', 'key' => 't.views.admin.participant_groups.are_participant_groups_required'])@endcomponent

        @endif

    </b-card>

@endsection
