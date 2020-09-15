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
        <template #header>{{__('t.views.admin.observation_orders.existing', ['courseName' => $course->name])}}</template>

        @if (count($course->observationOrders))
            @php
                $fields = [
                    __('t.models.observation_order.order_name') => function(\App\Models\ObservationOrder $observationOrder) { return $observationOrder->order_name; },
                    __('t.models.observation_order.user') => function(\App\Models\ObservationOrder $observationOrder) {
                        return $observationOrder->users->map(function ($item){ return $item['name'];
                        })->implode(', ');

                    },
                    __('t.models.observation_order.participants') => function(\App\Models\ObservationOrder $observationOrder) {
                        return $observationOrder->participants->map(function ($item){
                            $scout_name = $item['scout_name'];
                            $group = $item['group'];
                            return $group ? "$scout_name ($group)" : $scout_name;
                        })->implode(', ');

                    },
                    __('t.models.observation_order.block') => function(\App\Models\ObservationOrder $observationOrder) {
                        return $observationOrder->blocks->map(function ($item){
                            $block_name = $item['name'];
                            $block_number = $item['block_number'];
                            $day_number = $item['day_number'];
                            $number = "$day_number.$block_number";
                            return $number ? "($number) $block_name" : $block_name;
                        })->implode(', ');

                    },
                ];

            @endphp
            @component('components.responsive-table', [
                'data' => $course->observationOrders,
                'fields' => $fields,
                'actions' => [
                    'edit' => function(\App\Models\ObservationOrder $observationOrder) use ($course) { return route('admin.observationOrders.edit', ['course' => $course->id, 'observationOrder' => $observationOrder->id]); },
                    'delete' => function(\App\Models\ObservationOrder $observationOrder) use ($course) { return [
                        'text' => __('t.views.admin.observation_orders.really_delete', [ 'name' => $observationOrder->order_name]),
                        'route' => ['admin.observationOrders.destroy', ['course' => $course->id, 'observationOrder' => $observationOrder->id]],
                     ];},
                ]
            ])@endcomponent



        @else

            {{__('t.views.admin.observation_orders.no_observation_order')}}

            @component('components.help-text', ['id' => 'noGroupsHelp', 'key' => 't.views.admin.observation_orders.are_observation_orders_required'])@endcomponent

        @endif

    </b-card>

@endsection
