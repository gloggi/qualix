@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.observation_orders.new')}}</template>

        <form-basic :action="['admin.observationOrders.store', {course: {{ $course->id }}}]">
            <input-text name="order_name" label="{{__('t.models.observation_order.order_name')}}" required autofocus></input-text>

            <input-multi-select
                name="user"
                label="{{__('t.models.observation_order.user')}}"
                :options="{{ json_encode($course->users->map->only('id', 'name')) }}"
                display-field="name"
                multiple
                required
                :autofocus="true"></input-multi-select>

            <input-multi-select
                name="participants"
                label="{{__('t.models.observation_order.participants')}}"
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

        </form-basic>

    </b-card>

    <b-card>
        <template #header>{{__('t.views.admin.observation_orders.existing', ['courseName' => $course->name])}}</template>

        @if (count($course->observationOrders))

            <responsive-table
                :data="{{ json_encode($course->observationOrders()->with('users', 'participants', 'blocks')->get()) }}"
                :fields="[
                    { label: $t('t.models.observation_order.order_name'), value: observationOrder => observationOrder.order_name },
                    { label: $t('t.models.observation_order.user'), value: observationOrder => observationOrder.users.map(user => user.name).join(', ') },
                    { label: $t('t.models.observation_order.participants'), value: observationOrder => observationOrder.participants.map(participant => participant.name_and_group).join(', ') },
                    { label: $t('t.models.observation_order.block'), value: observationOrder => observationOrder.blocks.map(block => block.blockname_and_number).join(', ') },
                ]"
                :actions="{
                    edit: observationOrder => routeUri('admin.observationOrders.edit', {course: {{ $course->id }}, observationOrder: observationOrder.id}),
                    delete: observationOrder => ({
                        text: $t('t.views.admin.observation_orders.really_delete', observationOrder),
                        route: ['admin.observationOrders.delete', {course: {{ $course->id }}, observationOrder: observationOrder.id}]
                    })
                }"></responsive-table>

        @else

            {{__('t.views.admin.observation_orders.no_observation_order')}}

            @component('components.help-text', ['id' => 'noGroupsHelp', 'key' => 't.views.admin.observation_orders.are_observation_orders_required'])@endcomponent

        @endif

    </b-card>

@endsection
