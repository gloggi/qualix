@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.blocks.edit')}}</template>

        @component('components.form', ['route' => ['admin.block.update', ['course' => $course->id, 'block' => $block->id]]])

            <input-text name="full_block_number" value="{{ $block->full_block_number }}" label="{{__('t.models.block.full_block_number')}}"></input-text>

            <input-text name="name" value="{{ $block->name }}" label="{{__('t.models.block.name')}}" required autofocus></input-text>

            <input-date name="block_date" value="{{ $block->block_date->format('Y-m-d') }}" label="{{__('t.models.block.block_date')}}" required></input-date>

            <input-multi-select
                name="requirements"
                value="{{ $block->requirements->pluck('id')->join(',') }}"
                label="{{__('t.models.block.requirements')}}"
                :options="{{ json_encode($course->requirements->map->only('id', 'content')) }}"
                display-field="content"
                multiple></input-multi-select>

            <button-submit></button-submit>

        @endcomponent

    </b-card>

@endsection
