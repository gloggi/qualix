@extends('layouts.default')

@section('pagetitle'){{__('t.views.admin.requirement_statuses.page_title') }}@endsection

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.requirement_statuses.new')}}</template>

        <form-basic :action="['admin.requirement_statuses.store', { course: {{ $course->id }} }]">

            <input-text name="name" label="{{__('t.models.requirement_status.name')}}" required autofocus></input-text>

            <input-multi-select
                name="color"
                :preselect-first="true"
                label="{{__('t.models.requirement_status.color')}}"
                :options="{{ json_encode(array_map(function($color) { return ['id' => $color]; }, \App\Models\RequirementStatus::COLORS)) }}"
                display-field="id"
                no-result="{{__('t.views.admin.requirement_statuses.no_color_results')}}"
                required>
                <template #option="props">
                    <span class="color-square" :class="'bg-'+props.option.id"></span>
                </template>
                <template #single-label="props">
                    <span class="color-square" :class="'bg-'+props.option.id"></span>
                </template>
            </input-multi-select>

            <input-multi-select
                name="icon"
                :preselect-first="true"
                label="{{__('t.models.requirement_status.icon')}}"
                :options="{{ json_encode(array_map(function($icon) { return ['id' => $icon]; }, \App\Models\RequirementStatus::ICONS)) }}"
                display-field="id"
                no-result="{{__('t.views.admin.requirement_statuses.no_icon_results')}}"
                required>
                <template #option="props">
                    <span class="fas" :class="'fa-'+props.option.id"></span>
                </template>
                <template #single-label="props">
                    <span class="fas" :class="'fa-'+props.option.id"></span>
                </template>
            </input-multi-select>

            <button-submit label="{{__('t.global.add')}}">

                @component('components.help-text', ['id' => 'requirement_statusestatusesHelp', 'key' => 't.views.admin.requirement_statuses.what_are_requirement_statuses'])@endcomponent

            </button-submit>

        </form-basic>

    </b-card>

    <b-card>
        <template #header>{{__('t.views.admin.requirement_statuses.existing', ['courseName' => $course->name])}}</template>

        <responsive-table
            :data="{{ json_encode($course->requirement_statuses->map->append('num_feedback_requirements')) }}"
            :fields="[
                { label: $t('t.views.admin.requirement_statuses.status'), slot: 'status' },
                @if(!$course->archived){ label: $t('t.views.admin.requirement_statuses.num_uses'), value: requirement_status => requirement_status.num_feedback_requirements },@endif
            ]"
            :actions="{
                edit: requirement_status => routeUri('admin.requirement_statuses.edit', {course: {{ $course->id }}, requirement_status: requirement_status.id}),
                @if($course->requirement_statuses()->count() > 1)
                    delete: requirement_status => ({
                        text: (requirement_status.num_feedback_requirements === 0 ? ($t('t.views.admin.requirement_statuses.really_delete', requirement_status) + ' ') : '') + $tc('t.views.admin.requirement_statuses.feedback_requirements_using_requirement_status', requirement_status.num_feedback_requirements),
                        route: ['admin.requirement_statuses.delete', {course: {{ $course->id }}, requirement_status: requirement_status.id}],
                        disabled: requirement_status.num_feedback_requirements !== 0,
                    })
                @endif
            }">
            <template #status="{ row }"><span :class="['text-' + row.color, 'fa-' + row.icon]" class="fas mr-2"></span>@{{ row.name }}</template>
        </responsive-table>

    </b-card>

@endsection
