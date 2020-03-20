@extends('layouts.default')

@section('content')

    <b-card class="container-fluid">
        <template #header>{{__('t.views.quali_details.title')}}</template>

        <div class="row my-3">

            <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                <div class="square-container">
                    <img class="card-img-top img img-responsive full-width" src="{{ $participant->image_url != null ? asset(Storage::url($participant->image_url)) : asset('images/was-gaffsch.svg') }}" alt="{{ $participant->scout_name }}">
                </div>
            </div>

            <div class="col">
                <div class="row">
                    <div class="col-sm-12 col-lg-6">
                        <h3>{{__('t.views.quali_details.participant_quali', ['participant' => $participant->scout_name, 'quali' => $quali->name])}}</h3>

                        <p>
                            <a href="{{ route('participants.detail', ['course' => $course->id, 'participant' => $participant->id]) }}">
                                <i class="fas fa-arrow-left"></i> {{__('t.views.quali_details.back_to_participant', ['name' => $participant->scout_name])}}
                            </a>
                        </p>
                    </div>

                    <div class="col">
                        @if($quali->user)
                            <div class="d-flex justify-content-end">
                                @if($quali->user->image_url)
                                    @component('components.img',  ['src' => asset(Storage::url($quali->user->image_url)), 'classes' => ['avatar-small mr-2']])@endcomponent
                                @endif
                                <span>
                                    <div>{{__('t.models.quali.user')}}:</div>
                                    <div>{{ $quali->user->name }}</div>
                                </span>
                            </div>
                        @endif
                    </div>

                </div>

                @if($quali->requirements()->count())
                    <h5>{{__('t.views.quali_details.requirements_status')}}</h5>

                    @component('components.requirement-progress', ['quali' => $quali])@endcomponent
                @endif

                <p class="mt-3">
                    {{ (new \App\Util\HtmlString)->nl2br_e($quali->notes) }}
                </p>
            </div>

        </div>

        @if($quali->requirements()->count())

            @component('includes.quali.requirements', ['qualiRequirements' => $quali->requirements])@endcomponent

        @endif

    </b-card>

@endsection
