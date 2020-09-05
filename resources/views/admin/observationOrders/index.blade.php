@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.observation_orders.new')}}</template>

        @component('components.form', ['route' => ['admin.observationOrders.store', ['course' => $course->id]]])
            <input-text @forminput('order_name') label="{{__('t.models.observation_order.order_name')}}" required autofocus></input-text>

            <input-multi-select
                @forminput('user')
            label="{{__('t.models.observation_order.user')}}"
            :options="{{ json_encode($course->users->map->only('id', 'name')) }}"

            display-field="name"
            multiple
            required
            :autofocus="true"></input-multi-select>
            <input-multi-select
                @forminput('participants')
            label="{{__('t.models.observation_order.participants')}}"
            :options="{{ json_encode($course->participants->map->only('id', 'scout_name')) }}"

            :groups="{{json_encode(
                    $course->participantGroups->mapWithKeys(function ($group) {
                        return [$group['group_name'] => $group->participants->pluck('id')->join(',')];
                    }))}}"

            display-field="scout_name"
            multiple
            required
            :autofocus="true"></input-multi-select>

            <input-multi-select
                @forminput('block')
            label="{{__('t.models.observation_order.block')}}"
            required
            :options="{{ json_encode($course->blocks->map->only('id', 'blockname_and_number')) }}"
            :autofocus="true"
            display-field="blockname_and_number"
            multiple
            :autofocus="true"
            ></input-multi-select>

            <button-submit label="{{__('t.global.add')}}">

                @component('components.help-text', ['id' => 'requirementsHelp', 'key' => 't.views.admin.observation_orders.what_are_observation_orders'])@endcomponent

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
