@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Beobachtung für TN erfassen')])

        @if (count($kurs->tns))

            @foreach($kurs->tns as $tn)

                <div><a href="{{ route('beobachtung.neu', ['kurs' => $kurs->id, 'tn' => $tn->id]) }}">{{ $tn->pfadiname }}</a></div>

            @endforeach

        @else

            {{__('Bisher sind keine Teilnehmende erfasst. Bitte erfasse sie')}} <a href="{{ route('admin.tn', ['kurs' => $kurs->id]) }}">{{__('hier')}}</a>.

        @endif

    @endcomponent

@endsection
