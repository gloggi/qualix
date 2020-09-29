@extends('layouts.print')

@section('pagetitle'){{ $quali->name }} {{ $participant->scout_name }}@endsection

@section('content')

    <div class="row mb-5">

        <div class="col-2 mb-3">
            <div class="square-container">
                <img class="card-img-top img img-responsive full-width" src="{{ $participant->image_url != null ? asset(Storage::url($participant->image_url)) : asset('images/was-gaffsch.svg') }}" alt="{{ $participant->scout_name }}">
            </div>
        </div>

        <div class="col">
            <div class="d-flex justify-content-between">
                <div>
                    <h2>{{__('t.views.quali_content.participant_quali', ['participant' => $participant->scout_name, 'quali' => $quali->name])}}</h2>
                    @if (isset($participant->group))<h5>{{ $participant->group }}</h5>@endif
                </div>

                <div>
                    <p class="font-weight-bold mb-0">{{ $course->name }}</p>
                    @if (isset($course->course_number))<p class="mb-0">{{ $course->course_number }}</p>@endif
                    @if($quali->user)
                        <div class="my-2">
                            <div>{{__('t.models.quali.user')}}:</div>
                            <div>{{ $quali->user->name }}</div>
                        </div>
                    @endif
                </div>

            </div>

            @if($quali->requirements()->count())
                <div class="mt-2">
                    <h5>{{__('t.views.quali_content.requirements_status')}}</h5>
                    <requirement-progress :requirements="{{ json_encode($quali->requirements) }}"></requirement-progress>
                </div>
            @endif
        </div>

    </div>

    <quali-editor
        readonly
        name=""
        :value="{{ json_encode($quali->contents) }}"
        :observations="{{ json_encode($observations) }}"
        :requirements="{{ json_encode($quali->requirements) }}"></quali-editor>

@endsection
