@extends('layouts.default')

@section('pagetitle'){{__('t.views.admin.requirement_statuses.page_title_edit') }}@endsection

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.requirement_statuses.edit')}}</template>

        <form-basic :action="['admin.requirement_statuses.update', { course: {{ $course->id }}, requirement_status: {{ $requirementStatus->id }} }]">

            <input-text name="name" value="{{ $requirementStatus->name }}" label="{{__('t.models.requirement_status.name')}}" required autofocus></input-text>

            <input-multi-select
                name="color"
                value="{{ $requirementStatus->color }}"
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
                value="{{ $requirementStatus->icon }}"
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

            <button-submit>
            </button-submit>

        </form-basic>

    </b-card>

@endsection
