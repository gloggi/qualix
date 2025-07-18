@extends('layouts.default')

@section('pagetitle'){{__('t.views.admin.blocks.page_title_edit') }}@endsection

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.blocks.edit')}}</template>

        <form-basic :action="['admin.block.update', {course: {{ $course->id }}, block: {{ $block->id }} }]">

            <input-text name="full_block_number" model-value="{{ $block->full_block_number }}" label="{{__('t.models.block.full_block_number')}}"></input-text>

            <input-text name="name" model-value="{{ $block->name }}" label="{{__('t.models.block.name')}}" required autofocus></input-text>

            <input-date name="block_date" model-value="{{ $block->block_date->format('Y-m-d') }}" label="{{__('t.models.block.block_date')}}" required></input-date>

            <input-multi-select
                name="requirements"
                model-value="{{ $block->requirements->pluck('id')->join(',') }}"
                label="{{__('t.models.block.requirements')}}"
                :options="{{ json_encode($course->requirements->map->only('id', 'content')) }}"
                display-field="content"
                multiple></input-multi-select>

            <button-submit></button-submit>

        </form-basic>

    </b-card>

@endsection
