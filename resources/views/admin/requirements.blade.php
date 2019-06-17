@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Neue Mindestanforderung')])

        @component('components.form', ['route' => ['admin.requirements.store', ['course' => $course->id]]])

            @component('components.form.textInput', ['name' => 'content', 'label' => __('Titel'), 'required' => true, 'autofocus' => true])@endcomponent

            @component('components.form.checkboxInput', ['name' => 'mandatory', 'label' => __('Killer-Kriterium')])@endcomponent

            @component('components.form.submit', ['label' => __('Hinzufügen')])@endcomponent

        @endcomponent

    @endcomponent

    @component('components.card', ['header' => __('Mindestanforderungen :courseName', ['courseName' => $course->name])])

        @if (count($course->requirements))

            @php
                $fields = [
                    __('Anforderung') => function(\App\Models\Requirement $requirement) { return $requirement->content; },
                    __('Killer') => function(\App\Models\Requirement $requirement) { return $requirement->mandatory ? __('Ja') : __('Nein'); },
                    __('Anzahl Beobachtungen') => function(\App\Models\Requirement $requirement) { return count($requirement->observations); },
                ];
                if ($course->archived) {
                    unset($fields[__('Anzahl Beobachtungen')]);
                }
            @endphp
            @component('components.responsive-table', [
                'data' => $course->requirements,
                'fields' => $fields,
                'actions' => [
                    'edit' => function(\App\Models\Requirement $requirement) use ($course) { return route('admin.requirements.edit', ['course' => $course->id, 'requirement' => $requirement->id]); },
                    'delete' => function(\App\Models\Requirement $requirement) use ($course) { return [
                        'text' => __('Willst du diese Mindestanforderung wirklich löschen?' . ($course->archived ? '' : ' ' . count($requirement->observations) . ' Beobachtung(en) ist / sind darauf zugewiesen.')),
                        'route' => ['admin.requirements.delete', ['course' => $course->id, 'requirement' => $requirement->id]],
                     ];},
                ]
            ])@endcomponent

        @else

            {{__('Bisher sind keine Mindestanforderungen erfasst.')}}

        @endif

    @endcomponent

@endsection
