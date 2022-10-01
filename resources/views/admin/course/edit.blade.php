@extends('layouts.default')

@section('pagetitle'){{__('t.views.page_titles.edit_course') }}@endsection


@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.course_settings.edit', ['name' => $course->name])}}</template>

        <form-basic :action="['admin.course.update', {course: {{ $course->id }} }]">

            <input-text name="name" value="{{ $course->name }}" label="{{__('t.models.course.name')}}" required autofocus></input-text>

            <input-text name="course_number" value="{{ $course->course_number }}" label="{{__('t.models.course.course_number')}}"></input-text>

            <row-text>
                <b-button variant="link" class="px-0" v-b-toggle.collapse-course-settings>
                    {{ __('t.views.admin.new_course.more_settings') }} <i class="fas fa-caret-down"></i>
                </b-button>
            </row-text>

            <b-collapse id="collapse-course-settings" :visible="false">

                <input-checkbox name="uses_impressions" label="{{__('t.models.course.uses_impressions')}}" value="{{ $course->uses_impressions }}" switch size="lg"></input-checkbox>

                <input-text name="observation_count_red_threshold" value="{{ $course->observation_count_red_threshold }}" label="{{__('t.models.course.observation_count_red_threshold')}}" required>
                    <template #append>
                        <b-input-group-text>{{ __('t.views.admin.course_settings.per_equipe_and_tn') }}</b-input-group-text>
                    </template>
                </input-text>

                <input-text name="observation_count_green_threshold" value="{{ $course->observation_count_green_threshold }}" label="{{__('t.models.course.observation_count_green_threshold')}}" required>
                    <template #append>
                        <b-input-group-text>{{ __('t.views.admin.course_settings.per_equipe_and_tn') }}</b-input-group-text>
                    </template>
                </input-text>

                <row-text>
                    @component('components.help-text', ['key' => 't.views.admin.course_settings.thresholds', 'id' => 'blockHelp'])@endcomponent
                </row-text>

            </b-collapse>

            <button-submit></button-submit>

        </form-basic>

    </b-card>

    <b-card>
        <template #header>{{__('t.views.admin.course_settings.archive_or_delete')}}</template>

        @if($course->archived)
            <p>{{__('t.views.admin.course_settings.is_archived', ['name' => $course->name])}}</p>
        @else
            <b-button class="btn btn-danger" v-b-modal.course-archive-modal>
                {{__('t.views.admin.course_settings.archive')}}
            </b-button>
            <b-modal id="course-archive-modal" title="{{ __('t.views.admin.course_settings.really_archive', ['name' => $course->name]) }}">
                {{__('t.views.admin.course_settings.archive_description')}}

                <template #modal-footer>
                    <form-basic :action="['admin.course.archive', {course: {{ $course->id }} }]">
                        <b-button type="submit" variant="danger">{{ __('t.views.admin.course_settings.archive_confirm') }}</b-button>
                    </form-basic>
                </template>
            </b-modal>
        @endif

        <b-button variant="danger" v-b-modal.course-delete-modal>
            {{__('t.views.admin.course_settings.delete')}}
        </b-button>
        <b-modal id="course-delete-modal" title="{{ __('t.views.admin.course_settings.really_delete', ['name' => $course->name]) }}">
            {{__('t.views.admin.course_settings.delete_description')}}

            <template #modal-footer>
                <form-basic :action="['admin.course.delete', {course: {{ $course->id }} }]">
                    <button type="submit" class="btn btn-danger">{{ __('t.views.admin.course_settings.delete_confirm') }}</button>
                </form-basic>
            </template>
        </b-modal>

        <div class="mt-3">
            @component('components.help-text', ['id' => 'archiveVsDeleteHelp', 'key' => 't.views.admin.course_settings.archive_vs_delete'])@endcomponent
        </div>

    </b-card>

@endsection
