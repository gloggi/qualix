@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.course_settings.edit', ['name' => $course->name])}}</template>

        @component('components.form', ['route' => ['admin.course.update', ['course' => $course->id]]])

            <input-text name="name" value="{{ $course->name }}" label="{{__('t.models.course.name')}}" required autofocus></input-text>

            <input-text name="course_number" value="{{ $course->course_number }}" label="{{__('t.models.course.course_number')}}"></input-text>

            <button-submit></button-submit>

        @endcomponent

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
                    @component('components.form', ['route' => ['admin.course.archive', ['course' => $course->id]]])
                        @csrf
                        <b-button type="submit" variant="danger">{{ __('t.views.admin.course_settings.archive_confirm') }}</b-button>
                    @endcomponent
                </template>
            </b-modal>
        @endif

        <b-button variant="danger" v-b-modal.course-delete-modal>
            {{__('t.views.admin.course_settings.delete')}}
        </b-button>
        <b-modal id="course-delete-modal" title="{{ __('t.views.admin.course_settings.really_delete', ['name' => $course->name]) }}">
            {{__('t.views.admin.course_settings.delete_description')}}

            <template #modal-footer>
                @component('components.form', ['route' => ['admin.course.delete', ['course' => $course->id]]])
                    <button type="submit" class="btn btn-danger">{{ __('t.views.admin.course_settings.delete_confirm') }}</button>
                @endcomponent
            </template>
        </b-modal>

        <div class="mt-3">
            @component('components.help-text', ['id' => 'archiveVsDeleteHelp', 'key' => 't.views.admin.course_settings.archive_vs_delete'])@endcomponent
        </div>

    </b-card>

@endsection
