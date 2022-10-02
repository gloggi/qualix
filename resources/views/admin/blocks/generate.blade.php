@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.block_generate.generate_from')}}</template>

        <form-basic :action="['admin.block.generate_store', { course: {{ $course->id }} }]" enctype="multipart/form-data">

            <input-text name="name" label="{{__('t.models.block.name')}}" required autofocus></input-text>

            <input-date name="blocks_startdate" value="{{ Auth::user()->getLastUsedBlockDate($course)->format('Y-m-d') }}" label="{{__('t.models.block.blocks_startdate')}}" required></input-date>

            <input-date name="blocks_enddate" value="{{ Auth::user()->getLastUsedBlockDate($course)->format('Y-m-d') }}" label="{{__('t.models.block.blocks_enddate')}}" required></input-date>

            <input-multi-select
                name="requirements"
                label="{{__('t.models.block.requirements')}}"
                :options="{{ json_encode($course->requirements->map->only('id', 'content')) }}"
                display-field="content"
                multiple></input-multi-select>

            <button-submit label="{{__('t.views.admin.block_generate.generate')}}"></button-submit>

        </form-basic>

    </b-card>

@endsection
