@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Neue Mindestanforderung')])

        @component('components.form', ['route' => ['admin.requirements.store', ['course' => $course->id]]])

            @component('components.form.textInput', ['name' => 'content', 'label' => __('Titel'), 'required' => true, 'autofocus' => true])@endcomponent

            @component('components.form.checkboxInput', ['name' => 'mandatory', 'label' => __('Killer-Kriterium')])@endcomponent

            @component('components.form.submit', ['label' => __('Hinzufügen')])

                @component('components.help-text', ['header' => 'Was sind Mindestanforderungen?', 'collapseId' => 'requirementHelp'])

                    {{__('Mindestanforderungen sind klare Voraussetzungen und Kriterien, die alle Teilnehmenden während dem Kurs erfüllen sollen. Anhand der Mindestanforderungen wird beurteilt, wer den Kurs besteht und wer nicht. Du kannst Mindestanforderungen als Killer-Kriterien markieren wenn du willst, aber es hat momentan keine Auswirkungen in Qualix selber (bis auf eine etwas andere Farbgebung).')}}

                @endcomponent

            @endcomponent

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

            @component('components.help-text', ['header' => __('Muss ich Mindestanforderungen für meinen Kurs erfassen?'), 'collapseId' => 'noRequirementsHelp'])

                {{__('Es ist sehr wichtig, vor dem Kurs im Kursteam Mindestanforderungen festzulegen, damit alle Teilnehmenden nach dem gleichen Schema qualifiziert werden und damit Entscheide im Kurs einfacher gefällt werden können. Aber wenn du diese nicht in Qualix führen willst, kannst du Beobachtungen auch ohne Mindestanforderungen erfassen.')}}

            @endcomponent

        @endif

    @endcomponent

@endsection
