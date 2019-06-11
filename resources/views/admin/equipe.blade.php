@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Equipe :courseName', ['courseName' => $course->name])])

        @component('components.responsive-table', [
            'id' => 'equipe',
            'data' => $course->users,
            'fields' => [
                __('Name') => function(\App\Models\User $user) { return $user->name; },
                __('E-Mail') => function(\App\Models\User $user) { return $user->email; },
            ],
            'actions' => [
                'delete' => function(\App\Models\User $user) use ($course) { return [
                    'text' => __('Willst du ' . $user->name . ' wirklich aus der Kursequipe entfernen?'),
                    'route' => ['admin.equipe.delete', ['course' => $course->id, 'user' => $user->id]],
                 ];},
            ]
        ])@endcomponent

    @endcomponent

    @component('components.card', ['header' => __('Einladungen')])

        @if (count($course->invitations))

            @component('components.responsive-table', [
                'id' => 'invitations',
                'data' => $course->invitations,
                'fields' => [
                    __('E-Mail') => function(\App\Models\Invitation $invitation) { return $invitation->email; },
                ],
                'actions' => [
                    'delete' => function(\App\Models\Invitation $invitation) use ($course) { return [
                        'text' => __('Willst du die Einladung für ' . $invitation->email . ' wirklich entfernen?'),
                        'route' => ['admin.invitation.delete', ['course' => $course->id, 'email' => $invitation->email]],
                     ]; },
                ]
            ])@endcomponent

        @else

            {{__('Momentan sind keine Einladungen offen.')}}

        @endif

    @endcomponent

    @component('components.card', ['header' => __('Equipenmitglied einladen')])

        @component('components.form', ['route' => ['admin.invitation.store', ['course' => $course->id]]])

            @component('components.form.textInput', ['name' => 'email', 'label' => __('E-Mail'), 'required' => true])@endcomponent

            @component('components.form.submit', ['label' => __('Einladen')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
