@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Beobachtung für TN erfassen')])

        @if (count($course->participants))

            <div class="row">

                @foreach($course->participants as $tn)

                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                        <a href="{{ route('tn.detail', ['course' => $course->id, 'tn' => $tn->id]) }}">
                            <div class="card">
                                <div class="imagebox">
                                    <div class="square-container">
                                        <img class="card-img-top img img-responsive full-width" src="{{ $tn->image_url != null ? asset(Storage::url($tn->image_url)) : asset('images/was-gaffsch.svg') }}" alt="{{ $tn->scout_name }}">
                                    </div>
                                    <span class="imagebox-label badge badge-primary my-2" style="font-size: 1.125rem">{{ count($tn->observations) }} <i class="fas fa-binoculars"></i></span>
                                </div>

                                <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                                    <h5 class="card-title my-2">{{ $tn->scout_name }}</h5>

                                </div>

                                <div class="card-body"><a href="{{ route('observation.new', ['course' => $course->id, 'tn' => $tn->id]) }}" class="btn btn-primary"><i class="fas fa-binoculars"></i> {{__('Beobachtung erfassen')}}</a></div>
                            </div>
                        </a>
                    </div>

                @endforeach

            </div>

        @else

            {{__('Bisher sind keine Teilnehmende erfasst. Bitte erfasse sie')}} <a href="{{ route('admin.participants', ['course' => $course->id]) }}">{{__('hier')}}</a>.

        @endif

    @endcomponent

@endsection
