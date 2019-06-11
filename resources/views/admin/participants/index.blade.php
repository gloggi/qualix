@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Teilnehmende :courseName', ['courseName' => $course->name])])

        @if (count($course->participants))

            @component('components.responsive-table', [
                'data' => $course->participants,
                'image' => [
                    __('Bild') => function(\App\Models\Participant $tn) { return ($tn->image_url!=null) ? view('components.img',  ['src' => asset(Storage::url($tn->image_url)), 'classes' => ['avatar-small']]) : ''; },
                ],
                'fields' => [
                    __('Pfadiname') => function(\App\Models\Participant $tn) { return $tn->scout_name; },
                    __('Abteilung') => function(\App\Models\Participant $tn) { return $tn->group; },
                ],
                'actions' => [
                    'edit' => function(\App\Models\Participant $tn) use ($course) { return route('admin.participants.edit', ['course' => $course->id, 'tn' => $tn->id]); },
                    'delete' => function(\App\Models\Participant $tn) use ($course) { return [
                        'text' => __('Willst du diese TN wirklich löschen? ' . count($tn->observations) . ' Beobachtung(en) ist / sind darauf zugewiesen.'),
                        'route' => ['admin.participants.delete', ['course' => $course->id, 'tn' => $tn->id]],
                     ];},
                ]
            ])@endcomponent

        @else

            {{__('Bisher sind keine Teilnehmende erfasst.')}}

        @endif

    @endcomponent

    @component('components.card', ['header' => __('Neue Teilnehmende')])

        @component('components.form', ['route' => ['admin.participants.store', ['course' => $course->id]], 'enctype' => 'multipart/form-data'])

            @component('components.form.textInput', ['name' => 'scout_name', 'label' => 'Pfadiname', 'required' => true])@endcomponent

            @component('components.form.textInput', ['name' => 'group', 'label' => 'Abteilung'])@endcomponent

            @component('components.form.fileInput', ['name' => 'image', 'label' => 'Bild', 'accept' => 'image/*'])@endcomponent

            @component('components.form.submit', ['label' => __('Hinzufügen')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
