@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Kursdetails :courseName', ['courseName' => $course->name])])

        @component('components.form', ['route' => ['admin.course.update', ['course' => $course->id]]])

            @component('components.form.textInput', ['name' => 'name', 'label' => __('Kursname'), 'required' => true, 'autofocus' => true, 'value' => $course->name])@endcomponent

            @component('components.form.textInput', ['name' => 'course_number', 'label' => __('Kursnummer'), 'value' => $course->course_number])@endcomponent

            @component('components.form.submit', ['label' => __('Speichern')])@endcomponent

        @endcomponent

    @endcomponent

    @component('components.card', ['header' => __('Kurs archivieren oder löschen', ['courseName' => $course->name])])

        @if($course->archived)
            <p>{{__(':name ist archiviert, das heisst alle personenbezogenen Daten der Teilnehmenden wurden gelöscht.', ['name' => $course->name])}}</p>
        @else
            <a class="btn btn-danger" data-toggle="modal" href="#course-archive-modal">
                {{__('Kurs archivieren…')}}
            </a>
            <div class="modal fade" id="course-archive-modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('Kurs :name wirklich archivieren?', ['name' => $course->name]) }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            {{__('Dies wird alle TN und Beobachtungen im Kurs komplett und dauerhaft löschen. Diese Aktion kann nicht rückgängig gemacht werden. Blöcke, Mindestanforderungen, Kategorien und Equipenmitglieder bleiben für zur späteren Einsicht bestehen.')}}
                        </div>
                        <div class="modal-footer">
                            @component('components.form', ['route' => ['admin.course.archive', ['course' => $course->id]]])
                                <button type="submit" class="btn btn-danger">{{ __('Definitiv archivieren') }}</button>
                            @endcomponent
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <a class="btn btn-danger" data-toggle="modal" href="#course-delete-modal">
            {{__('Kurs komplett löschen…')}}
        </a>
        <div class="modal fade" id="course-delete-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Kurs :name wirklich löschen?', ['name' => $course->name]) }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{__('Dies wird den Kurs komplett und dauerhaft löschen, inklusive alle Blöcke, TN, Mindestanforderungen, Kategorien, Teilnehmer und Beobachtungen darin. Diese Aktion kann nicht rückgängig gemacht werden.')}}
                    </div>
                    <div class="modal-footer">
                        @component('components.form', ['route' => ['admin.course.delete', ['course' => $course->id]]])
                            <button type="submit" class="btn btn-danger">{{ __('Definitiv löschen') }}</button>
                        @endcomponent
                    </div>
                </div>
            </div>
        </div>

    @endcomponent

@endsection
