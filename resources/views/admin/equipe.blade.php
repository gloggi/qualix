@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Equipe :courseName', ['courseName' => $kurs->name])])

        @component('components.responsive-table', [
            'id' => 'equipe',
            'data' => $kurs->users,
            'fields' => [
                __('Name') => function(\App\Models\User $user) { return $user->name; },
                __('E-Mail') => function(\App\Models\User $user) { return $user->email; },
            ],
            'actions' => [
                'delete' => function(\App\Models\User $user) use ($kurs) { return [
                    'text' => __('Willst du ' . $user->name . ' wirklich aus der Kursequipe entfernen?'),
                    'route' => ['admin.equipe.delete', ['kurs' => $kurs->id, 'user' => $user->id]],
                 ];},
            ]
        ])@endcomponent

    @endcomponent

    @component('components.card', ['header' => __('Einladungen')])

        @if (count($kurs->einladungen))

            @component('components.responsive-table', [
                'id' => 'invitations',
                'data' => $kurs->einladungen,
                'fields' => [
                    __('E-Mail') => function(\App\Models\Einladung $einladung) { return $einladung->email; },
                ],
                'actions' => [
                    'delete' => function(\App\Models\Einladung $einladung) use ($kurs) { return [
                        'text' => __('Willst du die Einladung für ' . $einladung->email . ' wirklich entfernen?'),
                        'route' => ['admin.invitation.delete', ['kurs' => $kurs->id, 'email' => $einladung->email]],
                     ]; },
                ]
            ])@endcomponent

        @else

            {{__('Momentan sind keine Einladungen offen.')}}

        @endif

    @endcomponent

    @component('components.card', ['header' => __('Equipenmitglied einladen')])

        @component('components.form', ['route' => ['admin.invitation.store', ['kurs' => $kurs->id]]])

            @component('components.form.textInput', ['name' => 'email', 'label' => __('E-Mail'), 'required' => true])@endcomponent

            @component('components.form.submit', ['label' => __('Einladen')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
