@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.observation_orders.edit')}}</template>

        <form-basic :action="['admin.observationOrders.update', {course: {{ $course->id }}, observationOrder: {{ $observationOrder->id }}}]">

            <input-text name="order_name" value="{{ $observationOrder->order_name }}" label="{{__('t.models.observation_order.order_name')}}" required autofocus></input-text>

            <input-multi-select
                name="user"
                value="{{ $observationOrder->users->pluck('id')->join(',') }}"
                label="{{__('t.models.observation_order.user')}}"
                required
                :options="{{ json_encode($course->users->map->only('id', 'name')) }}"
                display-field="name"
                multiple></input-multi-select>

            <input-multi-select
                name="participants"
                value="{{ $observationOrder->participants->pluck('id')->join(',') }}"
                label="{{__('t.models.observation_order.participants')}}"
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
                value="{{ $observationOrder->blocks->pluck('id')->join(',') }}"
                label="{{__('t.models.observation_order.block')}}"
                required
                :options="{{ json_encode($course->blocks->map->only('id', 'blockname_and_number')) }}"
                display-field="blockname_and_number"
                multiple></input-multi-select>

            <button-submit></button-submit>

        </form-basic>

    </b-card>

@endsection
