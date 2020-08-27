@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.participants.title')}}</template>

        @if (count($course->participants))

            <div class="row">

                @foreach($course->participants as $participant)

                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                        <a href="{{ route('participants.detail', ['course' => $course->id, 'participant' => $participant->id]) }}">
                            <div class="card">
                                <div class="imagebox">
                                    <div class="square-container">
                                        <img class="card-img-top img img-responsive full-width" src="{{ $participant->image_url != null ? asset(Storage::url($participant->image_url)) : asset('images/was-gaffsch.svg') }}" alt="{{ $participant->scout_name }}">
                                    </div>
                                    <span class="font-size-larger imagebox-label badge badge-primary my-2">{{ count($participant->observations) }} <i class="fas fa-binoculars"></i></span>
                                </div>

                                <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                                    <h5 class="card-title my-2">{{ $participant->scout_name }}</h5>

                                </div>

                                <div class="card-body"><a href="{{ route('observation.new', ['course' => $course->id, 'participant' => $participant->id]) }}" class="btn btn-primary"><i class="fas fa-binoculars"></i> {{__('t.global.add_observation')}}</a></div>
                            </div>
                        </a>
                    </div>

                @endforeach

            </div>

        @else

            {{__('t.views.participants.no_participants', ['here' => $participantManagementLink])}}

        @endif

    </b-card>

@endsection
