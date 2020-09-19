@extends('layouts.default')

@section('content')

    <b-card body-class="container-fluid">
        <template #header>
            <div class="d-flex justify-content-between">
                <span>{{__('t.views.quali_content.title')}}</span>
                <a class="btn-link" href="{{ route('qualiContent.edit', ['course' => $course->id, 'participant' => $participant->id, 'quali' => $quali->id]) }}">
                    {{__('t.global.edit')}} <i class="fas fa-edit"></i>
                </a>
            </div>
        </template>

        <div class="row my-3">

            <div class="col-3 mb-3">
                <div class="square-container">
                    <img class="card-img-top img img-responsive full-width" src="{{ $participant->image_url != null ? asset(Storage::url($participant->image_url)) : asset('images/was-gaffsch.svg') }}" alt="{{ $participant->scout_name }}">
                </div>
            </div>

            <div class="col">
                <div class="row">
                    <div class="col-sm-12 col-md-8">
                        <h3>{{__('t.views.quali_content.participant_quali', ['participant' => $participant->scout_name, 'quali' => $quali->name])}}</h3>

                        <p>
                            <a href="{{ route('participants.detail', ['course' => $course->id, 'participant' => $participant->id]) }}">
                                <i class="fas fa-arrow-left"></i> {{__('t.views.quali_content.back_to_participant', ['name' => $participant->scout_name])}}
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
                    <div class="d-none d-lg-block">
                        <h5>{{__('t.views.quali_content.requirements_status')}}</h5>
                        <requirement-progress :quali-requirements="{{ json_encode($quali->requirements) }}"></requirement-progress>
                    </div>
                @endif
            </div>

        </div>

        @if($quali->requirements()->count())
            <div class="d-lg-none">
                <h5>{{__('t.views.quali_content.requirements_status')}}</h5>
                <requirement-progress :quali-requirements="{{ json_encode($quali->requirements) }}"></requirement-progress>
            </div>
        @endif

        @component('includes.qualiContent.text', ['quali' => $quali, 'participant' => $participant, 'course' => $course])@endcomponent
    </b-card>

@endsection
