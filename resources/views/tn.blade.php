@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Beobachtung für TN erfassen')])

        @if (count($kurs->tns))

            <div class="card-deck">

                @foreach($kurs->tns as $tn)

                    @component('components.tn-card', ['name' => $tn->pfadiname, 'image' => $tn->bild_url, 'link' => route('beobachtung.neu', ['kurs' => $kurs->id, 'tn' => $tn->id])])@endcomponent

                @endforeach

            </div>

        @else

            {{__('Bisher sind keine Teilnehmende erfasst. Bitte erfasse sie')}} <a href="{{ route('admin.tn', ['kurs' => $kurs->id]) }}">{{__('hier')}}</a>.

        @endif

    @endcomponent

@endsection
