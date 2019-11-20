@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('t.views.admin.course_settings.edit', ['name' => $course->name])])

        @component('components.form', ['route' => ['admin.course.update', ['course' => $course->id]]])

            @component('components.form.textInput', ['name' => 'name', 'label' => __('t.models.course.name'), 'required' => true, 'autofocus' => true, 'value' => $course->name])@endcomponent

            @component('components.form.textInput', ['name' => 'course_number', 'label' => __('t.models.course.course_number'), 'value' => $course->course_number])@endcomponent

            @component('components.form.submit', ['label' => __('t.global.save')])@endcomponent

        @endcomponent

    @endcomponent

    @component('components.card', ['header' => __('t.views.admin.course_settings.archive_or_delete', ['courseName' => $course->name])])

        @if($course->archived)
            <p>{{__('t.views.admin.course_settings.is_archived', ['name' => $course->name])}}</p>
        @else
            <a class="btn btn-danger" data-toggle="modal" href="#course-archive-modal">
                {{__('t.views.admin.course_settings.archive')}}
            </a>
            <div class="modal fade" id="course-archive-modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('t.views.admin.course_settings.really_archive', ['name' => $course->name]) }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="{{__('t.global.close')}}">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            {{__('t.views.admin.course_settings.archive_description')}}
                        </div>
                        <div class="modal-footer">
                            @component('components.form', ['route' => ['admin.course.archive', ['course' => $course->id]]])
                                <button type="submit" class="btn btn-danger">{{ __('t.views.admin.course_settings.archive_confirm') }}</button>
                            @endcomponent
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <a class="btn btn-danger" data-toggle="modal" href="#course-delete-modal">
            {{__('t.views.admin.course_settings.delete')}}
        </a>
        <div class="modal fade" id="course-delete-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('t.views.admin.course_settings.really_delete', ['name' => $course->name]) }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="{{__('t.global.close')}}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{__('t.views.admin.course_settings.delete_description')}}
                    </div>
                    <div class="modal-footer">
                        @component('components.form', ['route' => ['admin.course.delete', ['course' => $course->id]]])
                            <button type="submit" class="btn btn-danger">{{ __('t.views.admin.course_settings.delete_confirm') }}</button>
                        @endcomponent
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            @component('components.help-text', ['id' => 'archiveVsDeleteHelp', 'key' => 't.views.admin.course_settings.archive_vs_delete'])@endcomponent
        </div>

    @endcomponent

@endsection
