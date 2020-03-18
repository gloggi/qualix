@extends('layouts.default')

@section('content')

    @component('includes.participant.details', ['participant' => $participant])@endcomponent

    @component('components.card', ['header' => __('t.views.participant_details.existing_observations')])

        @component('includes.participant.observations.filters', ['participant' => $participant, 'requirement' => $requirement, 'category' => $category])@endcomponent

        @if (count($observations))

            @component('includes.participant.observations.list', ['observations' => $observations])@endcomponent

        @else

            {{__('t.views.participant_details.no_observations')}}

        @endif

    @endcomponent

@endsection
