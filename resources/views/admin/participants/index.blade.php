@extends('layouts.default')

@section('pagetitle'){{__('t.views.page_titles.admin_participants') }}@endsection

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.participants.new')}}</template>

        <form-basic :action="['admin.participants.store', { course: {{ $course->id }} }]" enctype="multipart/form-data">

            <input-text name="scout_name" label="{{__('t.models.participant.scout_name')}}" required autofocus></input-text>

            <input-text name="group" label="{{__('t.models.participant.group')}}"></input-text>

            <input-file name="image" label="{{__('t.models.participant.image')}}" accept="image/*"></input-file>

            <input-textarea name="freetext" label="{{__('t.models.participant.freetext')}}"></input-textarea>

            <button-submit label="{{__('t.global.add')}}">
                <a class="btn btn-link mb-1" href="{{ route('admin.participants.import', ['course' => $course]) }}">
                    {{ __('t.views.admin.participants.import') }}
                </a>
            </button-submit>

        </form-basic>

    </b-card>

    <b-card>
        <template #header>{{__('t.views.admin.participants.existing', ['courseName' => $course->name])}}</template>

        @if (count($course->participants))

            <responsive-table
                :data="{{ json_encode($course->participants->map->append('num_observations')) }}"
                :fields="[
                    { label: $t('t.models.participant.image'), value: participant => participant.image_path, type: 'image' },
                    { label: $t('t.models.participant.scout_name'), value: participant => participant.scout_name },
                    { label: $t('t.models.participant.group'), value: participant => participant.group },
                ]"
                :actions="{
                    edit: participant => routeUri('admin.participants.edit', {course: {{ $course->id }}, participant: participant.id}),
                    delete: participant => ({
                        text: $t('t.views.admin.participants.really_remove', participant) @if(!$course->archived) + ' ' + $tc('t.views.admin.participants.observations_on_participant', participant.num_observations)@endif,
                        route: ['admin.participants.delete', {course: {{ $course->id }}, participant: participant.id}]
                    })
                }"
            ></responsive-table>

        @else

            {{__('t.views.admin.participants.no_participants')}}

        @endif

    </b-card>

@endsection
