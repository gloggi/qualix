@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Kategorien :courseName', ['courseName' => $course->name])])

        @if (count($course->categories))

            @component('components.responsive-table', [
                'data' => $course->categories,
                'fields' => [
                    __('Titel') => function(\App\Models\Category $category) { return $category->name; },
                    __('Anzahl Beobachtungen') => function(\App\Models\Category $category) { return count($category->observations); },
                ],
                'actions' => [
                    'edit' => function(\App\Models\Category $category) use ($course) { return route('admin.categories.edit', ['course' => $course->id, 'category' => $category->id]); },
                    'delete' => function(\App\Models\Category $category) use ($course) { return [
                        'text' => __('Willst du diese Kategorie wirklich löschen? ' . count($category->observations) . ' Beobachtung(en) ist / sind darauf zugewiesen.'),
                        'route' => ['admin.categories.delete', ['course' => $course->id, 'category' => $category->id]],
                     ];},
                ]
            ])@endcomponent

        @else

            {{__('Bisher sind keine Kategorien erfasst.')}}

        @endif

    @endcomponent

    @component('components.card', ['header' => __('Neue Kategorie')])

        @component('components.form', ['route' => ['admin.categories.store', ['course' => $course->id]]])

            @component('components.form.textInput', ['name' => 'name', 'label' => __('Titel'), 'required' => true])@endcomponent

            @component('components.form.submit', ['label' => __('Hinzufügen')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
