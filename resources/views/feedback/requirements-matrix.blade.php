@extends('layouts.default')

@section('wideLayout'){{ json_encode($feedbackRequirements->map->requirement_id->unique()->count() >= 6) }}@endsection

@section('content')

    <b-card>
        <template #header>{{__('t.views.feedback.requirements_matrix.title', ['name' => $feedbackData->name])}}</template>

        @if (count($feedbacks))

            <requirements-matrix
                :feedback-requirements="{{ json_encode($feedbackRequirements) }}"
                :feedbacks="{{ json_encode($feedbacks) }}"
                :all-requirements="{{ json_encode($allRequirements) }}"
                :all-participants="{{ json_encode($allParticipants) }}"
                :requirement-statuses="{{ json_encode($course->requirement_statuses) }}"></requirements-matrix>

        @else

            {{__('t.views.overview.no_participants', ['here' => $participantManagementLink])}}

        @endif

    </b-card>

@endsection
