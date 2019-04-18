@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Beobachtung für TN erfassen')])

        @if (count($kurs->tns))

            <div class="row">

                @foreach($kurs->tns as $tn)

                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                        <a href="{{ route('beobachtung.neu', ['kurs' => $kurs->id, 'tn' => $tn->id]) }}">
                            <div class="card">
                                <div class="square-container">
                                    <img class="card-img-top img img-responsive full-width" src="{{ $tn->bild_url != null ? asset(Storage::url($tn->bild_url)) : asset('images/was-gaffsch.svg') }}" alt="{{ $tn->pfadiname }}">
                                </div>

                                <div class="card-body">
                                    <h5 class="card-title">{{ $tn->pfadiname }}</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                @endforeach

            </div>

        @else

            {{__('Bisher sind keine Teilnehmende erfasst. Bitte erfasse sie')}} <a href="{{ route('admin.tn', ['kurs' => $kurs->id]) }}">{{__('hier')}}</a>.

        @endif

    @endcomponent

@endsection
