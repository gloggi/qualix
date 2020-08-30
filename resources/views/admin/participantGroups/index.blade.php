@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.participantGroups.new')}}</template>

        @component('components.form', ['route' => ['admin.participantGroups.store', ['course' => $course->id]]])

            <input-text @forminput('group_name') label="{{__('t.models.participantGroup.group_name')}}" required autofocus></input-text>

            <input-multi-select
                @forminput('participants', $participants)
            label="{{__('t.models.participantGroup.participants')}}"
            :options="{{ json_encode($course->participants->map->only('id', 'scout_name')) }}"
            display-field="scout_name"
            required
            multiple
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
                    __('t.models.participantGroup.group_name') => function(\App\Models\ParticipantGroup $participantGroup) { return $participantGroup->group_name; },
                    __('t.models.participantGroup.participants') => function(\App\Models\ParticipantGroup $participantGroup) {
                    $names = $participantGroup->participants->map(function ($item){
                        $scout_name = $item['scout_name'];
                        return $item['group'] ? $scout_name." ".$item['group'] : $scout_name;
                    });


                    return $names->implode(', '); },
                ];

            @endphp
            @component('components.responsive-table', [
                'data' => $course->participantGroups,
                'fields' => $fields,
                'actions' => [
                    'edit' => function(\App\Models\ParticipantGroup $participantGroup) use ($course) { return route('admin.participantGroups.edit', ['course' => $course->id, 'participantGroup' => $participantGroup->id]); },
                    'delete' => function(\App\Models\ParticipantGroup $participantGroup) use ($course) { return [
                        'text' => __('t.views.admin.participantGroups.really_delete') . ($course->archived ? '' : ' ' . trans_choice('t.views.admin.participantGroups.participants_on_group', $participantGroup->participants)),
                        'route' => ['admin.participantGroups.destroy', ['course' => $course->id, 'participantGroup' => $participantGroup->id]],
                     ];},
                ]
            ])@endcomponent



        @else

            {{__('t.views.admin.participantGroups.no_participantGroup')}}

            @component('components.help-text', ['id' => 'noGroupsHelp', 'key' => 't.views.admin.participantGroups.are_participantGroups_required'])@endcomponent

        @endif

    </b-card>

@endsection
