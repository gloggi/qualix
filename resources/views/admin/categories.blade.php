@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Neue Kategorie')])

        @component('components.form', ['route' => ['admin.categories.store', ['course' => $course->id]]])

            @component('components.form.textInput', ['name' => 'name', 'label' => __('Titel'), 'required' => true, 'autofocus' => true])@endcomponent

            @component('components.form.submit', ['label' => __('Hinzufügen')])

                @component('components.help-text', ['header' => 'Was sind Kategorien?', 'collapseId' => 'categoryHelp'])

                    {{__('Kategorien können auf verschiedene Art eingesetzt werden. Jeder Beobachtung kann eine, mehrere oder keine Kategorie zugewiesen werden. Das kann man zum Beispiel zur Einordnung in verschiedene Abschnitte eines Quali-Formulars verwenden (wenn die Abschnitte nicht sowieso den Mindestanforderungen entsprechen). Oder um zu markieren, ob eine Beobachtung schon im Zwischenquali angesprochen wurde. Oder noch ganz andere Anwendungen, die dir einfallen. Danach kannst du die Beobachtungs-Liste eines Teilnehmenden nach Kategorien filtern.')}}

                @endcomponent

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

            @component('components.help-text', ['header' => __('Muss ich Kategorien für meinen Kurs erfassen?'), 'collapseId' => 'noCategoriesHelp'])

                {{__('Nein, Kategorien sind komplett optional, falls ihr in eurem Kursteam keine Verwendung dafür habt.')}}

            @endcomponent

        @endif

    @endcomponent

@endsection
