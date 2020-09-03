@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.participant_groups.new')}}</template>

        @component('components.form', ['route' => ['admin.participantGroups.store', ['course' => $course->id]]])

            <input-text @forminput('group_name') label="{{__('t.models.participant_group.group_name')}}" required autofocus></input-text>

            <input-multi-select
                @forminput('participants')
                label="{{__('t.models.participant_group.participants')}}"
                :options="{{ json_encode($course->participants->map->only('id', 'scout_name')) }}"
                display-field="scout_name"
                required
                multiple
                :autofocus="true"></input-multi-select>

            <button-submit label="{{__('t.global.add')}}">

                @component('components.help-text', ['id' => 'requirementsHelp', 'key' => 't.views.admin.participant_groups.what_are_participant_groups'])@endcomponent

            </button-submit>

        @endcomponent

    </b-card>

    <b-card>
        <template #header>{{__('t.views.admin.participant_groups.existing', ['courseName' => $course->name])}}</template>

        @if (count($course->participantGroups))

            @php
                $fields = [
                    __('t.models.participant_group.group_name') => function(\App\Models\ParticipantGroup $participantGroup) { return $participantGroup->group_name; },
                    __('t.models.participant_group.participants') => function(\App\Models\ParticipantGroup $participantGroup) {
                        return $participantGroup->participants->map(function ($item){
                            $scout_name = $item['scout_name'];
                            $group = $item['group'];
                            return $group ? "$scout_name ($group)" : $scout_name;
                        })->implode(', ');
                    },
                ];

            @endphp
            @component('components.responsive-table', [
                'data' => $course->participantGroups,
                'fields' => $fields,
                'actions' => [
                    'edit' => function(\App\Models\ParticipantGroup $participantGroup) use ($course) { return route('admin.participantGroups.edit', ['course' => $course->id, 'participantGroup' => $participantGroup->id]); },
                    'delete' => function(\App\Models\ParticipantGroup $participantGroup) use ($course) { return [
                        'text' => __('t.views.admin.participant_groups.really_delete', [ 'name' => $participantGroup->group_name]),
                        'route' => ['admin.participantGroups.destroy', ['course' => $course->id, 'participantGroup' => $participantGroup->id]],
                     ];},
                ]
            ])@endcomponent



        @else

            {{__('t.views.admin.participant_groups.no_participant_group')}}

            @component('components.help-text', ['id' => 'noGroupsHelp', 'key' => 't.views.admin.participant_groups.are_participant_groups_required'])@endcomponent

        @endif

    </b-card>

@endsection
