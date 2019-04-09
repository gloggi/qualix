@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Qualikategorien :courseName', ['courseName' => $kurs->name])])

        @if (count($kurs->qks))

            @component('components.responsive-table', [
                'data' => $kurs->qks,
                'fields' => [
                    __('Titel') => function(\App\Models\QK $qk) { return $qk->quali_kategorie; },
                    __('Anzahl Beobachtungen') => function(\App\Models\QK $qk) { return count($qk->beobachtungen); },
                ],
                'actions' => [
                    'edit' => function(\App\Models\QK $qk) use ($kurs) { return route('admin.qk.edit', ['kurs' => $kurs->id, 'qk' => $qk->id]); },
                    'delete' => function(\App\Models\QK $qk) use ($kurs) { return [
                        'text' => __('Willst du diese Qualikategorie wirklich löschen? ' . count($qk->beobachtungen) . ' Beobachtung(en) ist / sind darauf zugewiesen.'),
                        'route' => ['admin.qk.delete', ['kurs' => $kurs->id, 'qk' => $qk->id]],
                     ];},
                ]
            ])@endcomponent

        @else

            {{__('Bisher sind keine Qualikategorien erfasst.')}}

        @endif

    @endcomponent

    @component('components.card', ['header' => __('Neue Qualikategorie')])

        @component('components.form', ['route' => ['admin.qk.store', ['kurs' => $kurs->id]]])

            @component('components.form.textInput', ['name' => 'quali_kategorie', 'label' => __('Titel'), 'required' => true])@endcomponent

            @component('components.form.submit', ['label' => __('Hinzufügen')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
