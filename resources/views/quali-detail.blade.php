@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.quali_details.title')}}</template>

        <div class="row my-3">

            <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                <div class="square-container">
                    <img class="card-img-top img img-responsive full-width" src="{{ $participant->image_url != null ? asset(Storage::url($participant->image_url)) : asset('images/was-gaffsch.svg') }}" alt="{{ $participant->scout_name }}">
                </div>
            </div>

            <div class="col">
                <h3>{{__('t.views.quali_details.participant_quali', ['participant' => $participant->scout_name, 'quali' => $quali->name])}}</h3>

                <p>
                    <a href="{{ route('participants.detail', ['course' => $course->id, 'participant' => $participant->id]) }}">
                        <i class="fas fa-arrow-left"></i> {{__('t.views.quali_details.back_to_participant', ['name' => $participant->scout_name])}}
                    </a>
                </p>

                <p>
                    {{ (new \App\Util\HtmlString)->nl2br_e($quali->notes) }}
                </p>

                @if($quali->requirements()->count())
                    @component('components.requirement-progress', ['quali' => $quali])@endcomponent
                @endif
            </div>

        </div>

    </b-card>

@endsection
