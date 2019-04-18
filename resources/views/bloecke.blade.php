@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Beobachtung in Block erfassen')])

        @if (count($kurs->bloecke))

            @php
                $days = [];
                foreach($kurs->bloecke as $block) {
                    $days[$block->datum->timestamp][] = $block;
                }
            @endphp
            <div id="accordion">

                @foreach($days as $day)
                <div class="card">
                    <div class="card-header" id="heading{{ $day[0]->datum->timestamp }}">
                        <h5 class="mb-0" data-toggle="collapse" data-target="#collapse{{ $day[0]->datum->timestamp }}" aria-expanded="true" aria-controls="collapse{{ $day[0]->datum->timestamp }}">
                            {{ $day[0]->datum->formatLocalized('%A %d.%m.%Y') }}
                        </h5>
                    </div>

                    <div id="collapse{{ $day[0]->datum->timestamp }}" class="collapse{{ $day[0]->datum->gt(\Carbon\Carbon::now()->subDays(2)) ? ' show' : '' }}" aria-labelledby="headingOne">
                        <ul class="list-group list-group-flush">
                            @foreach ($day as $block)
                                <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ route('beobachtung.neu', ['kurs' => $kurs->id, 'block' => $block->id]) }}"><h5 class="mb-0">{{ $block->blockname_and_number }}</h5><span class="badge badge-primary" style="font-size: 1.125rem;">{{ count($block->beobachtungen) }} <i class="fas fa-binoculars"></i></span></a>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endforeach
            </div>

        @else

            {{__('Bisher sind keine Blöcke erfasst. Bitte erfasse sie')}} <a href="{{ route('admin.bloecke', ['kurs' => $kurs->id]) }}">{{__('hier')}}</a>.

        @endif

    @endcomponent

@endsection
