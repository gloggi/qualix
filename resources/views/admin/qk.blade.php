@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Qualikategorien :courseName', ['courseName' => $kurs->name])])

        @component('components.form', ['route' => 'admin.qk.store'])

            @component('components.form.hiddenInput', ['name' => 'kursId', 'value' => $kurs->id])@endcomponent

            @component('components.form.textInput', ['name' => 'name', 'label' => __('Name'), 'required' => true])@endcomponent

            @component('components.form.submit', ['label' => __('Hinzufügen')])@endcomponent

        @endcomponent

        @component('components.responsive-table', [
            'selectable' => true,
            'data' => $kurs->qks,
            'fields' => [
                'Quali-Kategorie' => function(\App\Models\QK $qk) { return $qk->quali_kategorie; },
                'QUALI-KATEGORIE' => function(\App\Models\QK $qk) { return strtoupper($qk->quali_kategorie); },
            ],
            'actions' => [
                'edit' => function(\App\Models\QK $qk) { return '#'; },
                'delete' => function(\App\Models\QK $qk) { return [
                    'text' => __('Willst du diese Quali-Kategorie wirklich löschen? ' . count($qk->beobachtungen) . ' Beobachtung(en) ist / sind darauf zugewiesen.'),
                    'route' => ['admin.qk.delete', ['id' => $qk->id]],
                 ];},
            ]
        ])@endcomponent

    @endcomponent

@endsection
