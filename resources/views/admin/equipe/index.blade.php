@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.equipe.existing', ['courseName' => $course->name])}}</template>

        @component('components.responsive-table', [
            'id' => 'equipe',
            'data' => $course->users,
            'fields' => [
                __('t.models.user.name') => function(\App\Models\User $user) { return $user->name; },
                __('t.models.user.email') => function(\App\Models\User $user) { return $user->email; },
            ],
            'actions' => [
                'delete' => function(\App\Models\User $user) use ($course) { return [
                    'text' => __('t.views.admin.equipe.really_delete', ['name' => $user->name]),
                    'route' => ['admin.equipe.delete', ['course' => $course->id, 'user' => $user->id]],
                 ];},
            ]
        ])@endcomponent

    </b-card>

    <b-card>
        <template #header>{{__('t.views.admin.equipe.existing_invitations')}}</template>

        @if (count($course->invitations))

            @component('components.responsive-table', [
                'id' => 'invitations',
                'data' => $course->invitations,
                'fields' => [
                    __('t.models.invitation.email') => function(\App\Models\Invitation $invitation) { return $invitation->email; },
                ],
                'actions' => [
                    'delete' => function(\App\Models\Invitation $invitation) use ($course) { return [
                        'text' => __('t.views.admin.equipe.really_delete_invitation', ['email' => $invitation->email]),
                        'route' => ['admin.invitation.delete', ['course' => $course->id, 'email' => $invitation->email]],
                     ]; },
                ]
            ])@endcomponent

        @else

            {{__('t.views.admin.equipe.no_invitations')}}

        @endif

    </b-card>

    <b-card>
        <template #header>{{__('t.views.admin.equipe.new_invitation')}}</template>

        @component('components.form', ['route' => ['admin.invitation.store', ['course' => $course->id]]])

            <input-text name="email" required label="{{__('t.models.invitation.email')}}"></input-text>

            <button-submit label="{{__('t.views.admin.equipe.invite')}}"></button-submit>

        @endcomponent

    </b-card>

@endsection
