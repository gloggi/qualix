@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.participantGroups.new')}}</template>

        @component('components.form', ['route' => ['admin.participantGroups.store', ['course' => $course->id]]])

            <input-text @forminput('content') label="{{__('t.models.participantGroup.content')}}" required autofocus></input-text>

            <input-multi-select
                @forminput('participants', $participants)
            label="{{__('t.models.participantGroup.participants')}}"
            :options="{{ json_encode($course->participants->map->only('id', 'scout_name')) }}"
            display-field="scout_name"
            multiple
            required
            :autofocus="{{ $participants === null ? 'true' : 'false' }}"></input-multi-select>

            <button-submit label="{{__('t.global.add')}}">

                @component('components.help-text', ['id' => 'requirementsHelp', 'key' => 't.views.admin.participantGroups.what_are_participantGroup'])@endcomponent

            </button-submit>

        @endcomponent

    </b-card>

    <b-card>
        <template #header>{{__('t.views.admin.participantGroups.existing', ['courseName' => $course->name])}}</template>

        @if (count($course->participantGroups))

            @php
                $fields = [
                    __('t.models.participantGroup.content') => function(\App\Models\ParticipantGroup $participantGroup) { return $participantGroup->group_name; },
                    __('t.models.participantGroup.participants') => function(\App\Models\ParticipantGroup $participantGroup) { return $participantGroup->group_name; },
                ];
                if ($course->archived) {
                    unset($fields[__('t.models.requirement.num_observations')]);
                }
            @endphp



        @else

            {{__('t.views.admin.participantGroups.no_participantGroup')}}

            @component('components.help-text', ['id' => 'noGroupsHelp', 'key' => 't.views.admin.participantGroups.are_participantGroups_required'])@endcomponent

        @endif

    </b-card>

@endsection
