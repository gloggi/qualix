@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Neue Teilnehmende')])

        @component('components.form', ['route' => ['admin.participants.store', ['course' => $course->id]], 'enctype' => 'multipart/form-data'])

            @component('components.form.textInput', ['name' => 'scout_name', 'label' => 'Pfadiname', 'required' => true, 'autofocus' => true])@endcomponent

            @component('components.form.textInput', ['name' => 'group', 'label' => 'Abteilung'])@endcomponent

            @component('components.form.fileInput', ['name' => 'image', 'label' => 'Bild', 'accept' => 'image/*'])@endcomponent

            @component('components.form.submit', ['label' => __('Hinzufügen')])@endcomponent

        @endcomponent

    @endcomponent

    @component('components.card', ['header' => __('Teilnehmende :courseName', ['courseName' => $course->name])])

        @if (count($course->participants))

            @component('components.responsive-table', [
                'data' => $course->participants,
                'image' => [
                    __('Bild') => function(\App\Models\Participant $participant) { return ($participant->image_url!=null) ? view('components.img',  ['src' => asset(Storage::url($participant->image_url)), 'classes' => ['avatar-small']]) : ''; },
                ],
                'fields' => [
                    __('Pfadiname') => function(\App\Models\Participant $participant) { return $participant->scout_name; },
                    __('Abteilung') => function(\App\Models\Participant $participant) { return $participant->group; },
                ],
                'actions' => [
                    'edit' => function(\App\Models\Participant $participant) use ($course) { return route('admin.participants.edit', ['course' => $course->id, 'participant' => $participant->id]); },
                    'delete' => function(\App\Models\Participant $participant) use ($course) { return [
                        'text' => __('Willst du diese/n TN wirklich löschen?' . ($course->archived ? '' : ' ' . count($participant->observations) . ' Beobachtung(en) ist / sind darauf zugewiesen.')),
                        'route' => ['admin.participants.delete', ['course' => $course->id, 'participant' => $participant->id]],
                     ];},
                ]
            ])@endcomponent

        @else

            {{__('Bisher sind keine Teilnehmende erfasst.')}}

        @endif

    @endcomponent

@endsection
