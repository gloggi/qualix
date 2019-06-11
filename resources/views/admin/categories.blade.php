@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Qualikategorien :courseName', ['courseName' => $course->name])])

        @if (count($course->categories))

            @component('components.responsive-table', [
                'data' => $course->categories,
                'fields' => [
                    __('Titel') => function(\App\Models\Category $qk) { return $qk->name; },
                    __('Anzahl Beobachtungen') => function(\App\Models\Category $qk) { return count($qk->observations); },
                ],
                'actions' => [
                    'edit' => function(\App\Models\Category $qk) use ($course) { return route('admin.categories.edit', ['course' => $course->id, 'qk' => $qk->id]); },
                    'delete' => function(\App\Models\Category $qk) use ($course) { return [
                        'text' => __('Willst du diese Qualikategorie wirklich löschen? ' . count($qk->observations) . ' Beobachtung(en) ist / sind darauf zugewiesen.'),
                        'route' => ['admin.categories.delete', ['course' => $course->id, 'qk' => $qk->id]],
                     ];},
                ]
            ])@endcomponent

        @else

            {{__('Bisher sind keine Qualikategorien erfasst.')}}

        @endif

    @endcomponent

    @component('components.card', ['header' => __('Neue Qualikategorie')])

        @component('components.form', ['route' => ['admin.categories.store', ['course' => $course->id]]])

            @component('components.form.textInput', ['name' => 'name', 'label' => __('Titel'), 'required' => true])@endcomponent

            @component('components.form.submit', ['label' => __('Hinzufügen')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
