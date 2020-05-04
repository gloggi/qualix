@extends('layouts.default')

@section('content')

    <b-card body-class="container-fluid">
        <template #header>{{__('t.views.quali_content.title')}}</template>

        <div class="row my-3">

            <div class="col-4 col-md-2 col-lg-1 mb-3">
                <div class="square-container">
                    <img class="card-img-top img img-responsive full-width" src="{{ $participant->image_url != null ? asset(Storage::url($participant->image_url)) : asset('images/was-gaffsch.svg') }}" alt="{{ $participant->scout_name }}">
                </div>
            </div>

            <div class="col">
                <div class="row">
                    <div class="col-sm-12 col-md-8">
                        <h3>{{__('t.views.quali_content.participant_quali', ['participant' => $participant->scout_name, 'quali' => $quali->name])}}</h3>
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
            </div>

        </div>

        @component('includes.qualiContent.edit', ['quali' => $quali, 'participant' => $participant, 'course' => $course, 'translations' => $translations])@endcomponent

    </b-card>

@endsection
