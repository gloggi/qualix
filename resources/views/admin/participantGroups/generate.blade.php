@extends('layouts.default')

@section('pagetitle'){{__('t.views.admin.participant_group_generator.page_title') }}@endsection

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.participant_groups.generate')}}</template>

        <form-basic :action="['admin.participantGroups.storeMany', { course: {{ $course->id }} }]">

            <participant-group-generator
                :participants="{{ json_encode($course->participants) }}"
                :participant-groups="{{ json_encode($course->participantGroups) }}"
            ></participant-group-generator>

        </form-basic>
    </b-card>

@endsection
