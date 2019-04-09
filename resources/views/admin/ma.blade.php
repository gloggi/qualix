@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Mindestanforderungen :courseName', ['courseName' => $kurs->name])])

        @component('components.responsive-table', [
            'data' => $kurs->mas,
            'fields' => [
                __('Anforderung') => function(\App\Models\MA $ma) { return $ma->anforderung; },
                __('Killer') => function(\App\Models\MA $ma) { return $ma->killer ? __('Ja') : __('Nein'); },
                __('Anzahl Beobachtungen') => function(\App\Models\MA $ma) { return count($ma->beobachtungen); },
            ],
            'actions' => [
                'edit' => function(\App\Models\MA $ma) use ($kurs) { return route('admin.ma.edit', ['kurs' => $kurs->id, 'ma' => $ma->id]); },
                'delete' => function(\App\Models\MA $ma) use ($kurs) { return [
                    'text' => __('Willst du diese Mindestanforderung wirklich löschen? ' . count($ma->beobachtungen) . ' Beobachtung(en) ist / sind darauf zugewiesen.'),
                    'route' => ['admin.ma.delete', ['kurs' => $kurs->id, 'ma' => $ma->id]],
                 ];},
            ]
        ])@endcomponent

    @endcomponent

    @component('components.card', ['header' => __('Neue Mindestanforderung')])

        @component('components.form', ['route' => ['admin.ma.store', ['kurs' => $kurs->id]]])

            @component('components.form.textInput', ['name' => 'anforderung', 'label' => __('Titel'), 'required' => true])@endcomponent

            @component('components.form.checkboxInput', ['name' => 'killer', 'label' => __('Killer-Kriterium')])@endcomponent

            @component('components.form.submit', ['label' => __('Hinzufügen')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
