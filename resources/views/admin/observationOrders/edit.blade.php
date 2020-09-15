@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.observation_orders.edit')}}</template>

        @component('components.form', ['route' => ['admin.observationOrders.update', ['course' => $course->id, 'observationOrder' => $observationOrder->id]]])

            <input-text @forminput('order_name', $observationOrder->order_name) label="{{__('t.models.observation_order.order_name')}}" required autofocus></input-text>

            <input-multi-select
                @forminput('user', $observationOrder->users->pluck('id')->join(','))
            label="{{__('t.models.observation_order.user')}}"
            required
            :options="{{ json_encode($course->users->map->only('id', 'name')) }}"
            display-field="name"
            multiple></input-multi-select>
            <input-multi-select
                @forminput('participants', $observationOrder->participants->pluck('id')->join(','))
            label="{{__('t.models.observation_order.participants')}}"
            required
            :options="{{ json_encode($course->participants->map->only('id', 'scout_name')) }}"
            display-field="scout_name"
            multiple></input-multi-select>
            <input-multi-select
                @forminput('participants', $observationOrder->blocks->pluck('id')->join(','))
            label="{{__('t.models.observation_order.block')}}"
            required
            :options="{{ json_encode($course->blocks->map->only('id', 'blockname_and_number')) }}"
            display-field="blockname_and_number"
            multiple></input-multi-select>

            <button-submit></button-submit>

        @endcomponent

    </b-card>

@endsection
