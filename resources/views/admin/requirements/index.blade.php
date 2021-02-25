@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.requirements.new')}}</template>

        <form-basic :action="['admin.requirements.store', { course: {{ $course->id }} }]">

            <input-text name="content" label="{{__('t.models.requirement.content')}}" required autofocus></input-text>

            <input-checkbox name="mandatory" label="{{__('t.models.requirement.mandatory')}}"></input-checkbox>

            <input-multi-select
                name="blocks"
                label="{{__('t.models.requirement.blocks')}}"
                :options="{{ json_encode($course->blocks->map->only('id', 'name')) }}"
                display-field="name"
                multiple></input-multi-select>

            <button-submit label="{{__('t.global.add')}}">

                @component('components.help-text', ['id' => 'requirementsHelp', 'key' => 't.views.admin.requirements.what_are_requirements'])@endcomponent

            </button-submit>

        </form-basic>

    </b-card>

    <b-card>
        <template #header>{{__('t.views.admin.requirements.existing', ['courseName' => $course->name])}}</template>

        @if (count($course->requirements))

            <responsive-table
                :data="{{ json_encode($course->requirements) }}"
                :fields="[
                    { label: $t('t.models.requirement.content'), value: requirement => requirement.content },
                    { label: $t('t.models.requirement.mandatory'), value: requirement => requirement.mandatory ? $t('t.global.yes') : $t('t.global.no') },
                    @if(!$course->archived){ label: $t('t.models.requirement.num_observations'), value: requirement => requirement.num_observations },@endif
                ]"
                :actions="{
                    edit: requirement => routeUri('admin.requirements.edit', {course: {{ $course->id }}, requirement: requirement.id}),
                    delete: requirement => ({
                        text: $t('t.views.admin.requirements.really_delete', requirement) @if(!$course->archived) + ' ' + $tc('t.views.admin.requirements.observations_on_requirement', requirement.num_observations) + ' ' + $tc('t.views.admin.requirements.qualis_using_requirement', requirement.num_quali_datas)@endif,
                        route: ['admin.requirements.delete', {course: {{ $course->id }}, requirement: requirement.id}]
                    })
                }"></responsive-table>

        @else

            {{__('t.views.admin.requirements.no_requirements')}}

            @component('components.help-text', ['id' => 'noRequirementsHelp', 'key' => 't.views.admin.requirements.are_requirements_required'])@endcomponent

        @endif

    </b-card>

@endsection
