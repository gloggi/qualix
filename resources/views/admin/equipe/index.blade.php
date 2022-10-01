@extends('layouts.default')

@section('pagetitle'){{__('t.views.page_titles.equipe') }}@endsection


@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.equipe.existing', ['courseName' => $course->name])}}</template>

        <responsive-table
            id="equipe"
            :data="{{ json_encode($course->users) }}"
            :fields="[
                { label: $t('t.models.user.image'), value: user => user.image_path, type: 'image' },
                { label: $t('t.models.user.name'), value: user => user.name },
                { label: $t('t.models.user.email'), value: user => user.email },
            ]"
            :actions="{
                delete: user => ({
                    text: $t('t.views.admin.equipe.really_delete', user),
                    route: ['admin.equipe.delete', {course: {{ $course->id }}, user: user.id}]
                })
            }"
        ></responsive-table>

    </b-card>

    <b-card>
        <template #header>{{__('t.views.admin.equipe.existing_invitations')}}</template>

        @if (count($course->invitations))


            <responsive-table
                id="invitations"
                :data="{{ json_encode($course->invitations) }}"
                :fields="[
                    { label: $t('t.models.invitation.email'), value: invitation => invitation.email },
                ]"
                :actions="{
                    delete: invitation => ({
                        text: $t('t.views.admin.equipe.really_delete_invitation', invitation),
                        route: ['admin.invitation.delete', {course: {{ $course->id }}, email: invitation.email}]
                    })
                }"
            ></responsive-table>

        @else

            {{__('t.views.admin.equipe.no_invitations')}}

        @endif

    </b-card>

    <b-card>
        <template #header>{{__('t.views.admin.equipe.new_invitation')}}</template>

        <form-basic :action="['admin.invitation.store', { course: {{ $course->id }} }]">

            <input-text name="email" required label="{{__('t.models.invitation.email')}}"></input-text>

            <button-submit label="{{__('t.views.admin.equipe.invite')}}"></button-submit>

        </form-basic>

    </b-card>

@endsection
