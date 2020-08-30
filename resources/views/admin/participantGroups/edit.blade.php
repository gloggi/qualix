@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.particpant_group.edit')}}</template>

        @component('components.form', ['route' => ['admin.requirements.update', ['course' => $course->id, 'requirement' => $requirement->id]]])

            <input-text @forminput('content', $requirement->content) label="{{__('t.models.requirement.content')}}" required autofocus></input-text>

            <input-checkbox @forminput('mandatory', $requirement->mandatory) label="{{__('t.models.requirement.mandatory')}}"></input-checkbox>

            <input-multi-select
                @forminput('blocks', $requirement->blocks->pluck('id')->join(','))
                label="{{__('t.models.requirement.blocks')}}"
                :options="{{ json_encode($course->blocks->map->only('id', 'name')) }}"
                display-field="name"
                multiple></input-multi-select>

            <button-submit></button-submit>

        @endcomponent

    </b-card>

@endsection
