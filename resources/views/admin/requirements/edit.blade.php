@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.requirements.edit')}}</template>

        <form-basic :action="['admin.requirements.update', { course: {{ $course->id }}, requirement: {{ $requirement->id }} }]">

            <input-text name="content" value="{{ $requirement->content }}" label="{{__('t.models.requirement.content')}}" required autofocus></input-text>

            <input-checkbox name="mandatory" value="{{ $requirement->mandatory }}" label="{{__('t.models.requirement.mandatory')}}"></input-checkbox>

            <input-multi-select
                name="blocks"
                value="{{ $requirement->blocks->pluck('id')->join(',') }}"
                label="{{__('t.models.requirement.blocks')}}"
                :options="{{ json_encode($course->blocks->map->only('id', 'name')) }}"
                display-field="name"
                multiple></input-multi-select>

            <button-submit></button-submit>

        </form-basic>

    </b-card>

@endsection
