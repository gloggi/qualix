@extends('layouts.default')

@section('content')

    <b-card body-class="container-fluid">
        <template #header>{{__('t.views.participant_details.title')}}</template>

        <div class="row my-3">

            <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                <div class="square-container">
                    <img class="card-img-top img img-responsive full-width" src="{{ $participant->image_url != null ? asset(Storage::url($participant->image_url)) : asset('images/was-gaffsch.svg') }}" alt="{{ $participant->scout_name }}">
                </div>
            </div>

            <div class="col">
                <h3>{{ $participant->scout_name }}</h3>
                @if (isset($participant->group))<h5>{{ $participant->group }}</h5>@endif
                @if (isset($participant->freetext))<p>{{ $participant->freetext }}</p>@endif
                <a href="{{route('admin.participants.edit', ['course' => $course->id, 'participant' => $participant->id])}}">
                    <i class="fas fa-edit"></i>
                </a>

                <p>{{ trans_choice('t.views.participant_details.num_observations', $participant->observations, ['positive' => $participant->positive->count(), 'neutral' => $participant->neutral->count(), 'negative' => $participant->negative->count()])}}</p>

                <table-observation-overview
                    :users="{{ json_encode($course->users) }}"
                    :participants="{{ json_encode([ $participant->observationCountsByUser() ]) }}"
                    :red-threshold="{{ json_encode($course->observation_count_red_threshold) }}"
                    :green-threshold="{{ json_encode($course->observation_count_green_threshold) }}"></table-observation-overview>

                <a href="{{ route('observation.new', ['course' => $course->id, 'participant' => $participant->id]) }}" class="btn btn-primary"><i class="fas fa-binoculars"></i> {{__('t.global.add_observation')}}</a>
            </div>

        </div>

        <div class="row">
            <div class="col">
                @foreach ($participant->participant_groups as $group)
                    <a href="{{ route('observation.new', ['course' => $course->id, 'participant' => $group->participants->implode('id',',')]) }}" class="btn btn-secondary my-1"><i class="fas fa-binoculars"></i> {{__($group->group_name)}}</a>
                @endforeach
            </div>
        </div>

    </b-card>


    @if ($participant->qualis()->count())

        <b-card>
            <template #header>{{__('t.views.participant_details.qualis.title')}}</template>

            <responsive-table
                :data="{{ json_encode($participant->qualis) }}"
                :fields="[
                    { label: $t('t.models.quali.name'), value: quali => quali.name, href: quali => routeUri('qualiContent.edit', {course: {{ $course->id }}, participant: {{ $participant->id }}, quali: quali.id}) },
                    @if($participant->qualis()->whereHas('requirements')->exists()){ label: $t('t.models.quali.requirement_progress'), slot: 'requirement-progress'},@endif
                    @if($participant->qualis()->whereHas('users')->exists()){ label: $t('t.models.quali.users'), value: quali => quali.users ? quali.users.map(u => u.name).join(', ') : '' },@endif
                ]"
                :actions="{
                    print: quali => routeUri('qualiContent.print', {course: {{ $course->id }}, participant: {{ $participant->id }}, quali: quali.id}),
                    edit: quali => routeUri('qualiContent.edit', {course: {{ $course->id }}, participant: {{ $participant->id }}, quali: quali.id}),
                }">

                <template v-slot:requirement-progress="{ row: quali }">
                    <requirement-progress v-if="quali.requirements.length" :requirements="quali.requirements"></requirement-progress>
                </template>

            </responsive-table>

        </b-card>

    @endif

    <b-card>
        <template #header>{{__('t.views.participant_details.existing_observations')}}</template>

        @if (count($observations))

            <participant-detail-observation-list
                :qualis-using-observations="{{ json_encode($qualis_using_observations, JSON_FORCE_OBJECT) }}"
                course-id="{{ $course->id }}"
                :observations="{{ json_encode($observations) }}"
                :requirements="{{ json_encode($course->requirements) }}"
                :categories="{{ json_encode($course->categories) }}"
                show-content
                show-block
                :show-requirements="{{ $course->uses_requirements ? 'true' : 'false' }}"
                :show-categories="{{ $course->uses_categories ? 'true' : 'false' }}"
                :show-impression="{{ $course->uses_impressions ? 'true' : 'false' }}"
                show-user></participant-detail-observation-list>

        @else
            {{__('t.views.participant_details.no_observations')}}
        @endif

    </b-card>

@endsection
