@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Neue Kategorie')])

        @component('components.form', ['route' => ['admin.categories.store', ['course' => $course->id]]])

            @component('components.form.textInput', ['name' => 'name', 'label' => __('Titel'), 'required' => true, 'autofocus' => true])@endcomponent

            @component('components.form.submit', ['label' => __('Hinzufügen')])

                @component('components.help-text', ['id' => 'categoryHelp', 'key' => 't.views.admin.categories.what_are_categories'])@endcomponent

            @endcomponent

        @endcomponent

    @endcomponent

    @component('components.card', ['header' => __('Kategorien :courseName', ['courseName' => $course->name])])

        @if (count($course->categories))

            @php
                $fields = [
                    __('Titel') => function(\App\Models\Category $category) { return $category->name; },
                    __('Anzahl Beobachtungen') => function(\App\Models\Category $category) { return count($category->observations); },
                ];
                if ($course->archived) {
                    unset($fields[__('Anzahl Beobachtungen')]);
                }
            @endphp
            @component('components.responsive-table', [
                'data' => $course->categories,
                'fields' => $fields,
                'actions' => [
                    'edit' => function(\App\Models\Category $category) use ($course) { return route('admin.categories.edit', ['course' => $course->id, 'category' => $category->id]); },
                    'delete' => function(\App\Models\Category $category) use ($course) { return [
                        'text' => __('Willst du diese Kategorie wirklich löschen?' . ($course->archived ? '' : ' ' . count($category->observations) . ' Beobachtung(en) ist / sind darauf zugewiesen.')),
                        'route' => ['admin.categories.delete', ['course' => $course->id, 'category' => $category->id]],
                     ];},
                ]
            ])@endcomponent

        @else

            {{__('Bisher sind keine Kategorien erfasst.')}}

            @component('components.help-text', ['id' => 'noCategoriesHelp', 'key' => 't.views.admin.categories.are_categories_required'])@endcomponent

        @endif

    @endcomponent

@endsection
