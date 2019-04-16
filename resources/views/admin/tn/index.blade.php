@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Teilnehmende :courseName', ['courseName' => $kurs->name])])

        @if (count($kurs->tns))

            @component('components.responsive-table', [
                'data' => $kurs->tns,
                'bild' => [
                    __('Bild') => function(\App\Models\TN $tn) { return ($tn->bild_url!=null) ? view('components.img',  ['src' => asset(Storage::url($tn->bild_url)), 'classes' => ['avatar-small']]) : ''; },
                ],
                'fields' => [
                    __('Pfadiname') => function(\App\Models\TN $tn) { return $tn->pfadiname; },
                    __('Abteilung') => function(\App\Models\TN $tn) { return $tn->abteilung; },
                ],
                'actions' => [
                    'edit' => function(\App\Models\TN $tn) use ($kurs) { return route('admin.tn.edit', ['kurs' => $kurs->id, 'tn' => $tn->id]); },
                    'delete' => function(\App\Models\TN $tn) use ($kurs) { return [
                        'text' => __('Willst du diesen TN wirklich löschen? ' /*. count($tn->beobachtungen) */. ' Beobachtung(en) ist / sind darauf zugewiesen.'),
                        'route' => ['admin.tn.delete', ['kurs' => $kurs->id, 'tn' => $tn->id]],
                     ];},
                ]
            ])@endcomponent

        @else

            {{__('Bisher sind keine Teilnehmende erfasst.')}}

        @endif

    @endcomponent

    @component('components.card', ['header' => __('Neue Teilnehmende')])

        @component('components.form', ['route' => ['admin.tn.store', ['kurs' => $kurs->id]], 'enctype' => 'multipart/form-data'])

            @component('components.form.textInput', ['name' => 'pfadiname', 'label' => 'Pfadiname', 'required' => true])@endcomponent

            @component('components.form.textInput', ['name' => 'abteilung', 'label' => 'Abteilung'])@endcomponent

            @component('components.form.fileInput', ['name' => 'bild', 'label' => 'Bild', 'accept' => 'image/*'])@endcomponent

            @component('components.form.submit', ['label' => __('Hinzufügen')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
