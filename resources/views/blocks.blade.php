@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Beobachtung in Block erfassen')])

        @if (count($course->blocks))

            @php
                $days = [];
                foreach($course->blocks as $block) {
                    $days[$block->block_date->timestamp][] = $block;
                }
            @endphp
            <div id="accordion">

                @foreach($days as $day)
                <div class="card">
                    <div class="card-header" id="heading{{ $day[0]->block_date->timestamp }}">
                        <h5 class="mb-0" data-toggle="collapse" data-target="#collapse{{ $day[0]->block_date->timestamp }}" aria-expanded="true" aria-controls="collapse{{ $day[0]->block_date->timestamp }}">
                            {{ $day[0]->block_date->formatLocalized('%A %d.%m.%Y') }}
                        </h5>
                    </div>

                    <div id="collapse{{ $day[0]->block_date->timestamp }}" class="collapse{{ $day[0]->block_date->gt(\Carbon\Carbon::now()->subDays(2)) ? ' show' : '' }}" aria-labelledby="heading{{ $day[0]->block_date->timestamp }}">
                        <ul class="list-group list-group-flush">
                            @foreach ($day as $block)
                                <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ route('observation.new', ['course' => $course->id, 'block' => $block->id]) }}"><h5 class="mb-0">{{ $block->blockname_and_number }}</h5><span class="badge badge-primary" style="font-size: 1.125rem;">{{ count($block->observations) }} <i class="fas fa-binoculars"></i></span></a>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endforeach
            </div>

        @else

            {{__('Bisher sind keine Blöcke erfasst. Bitte erfasse sie')}} <a href="{{ route('admin.blocks', ['course' => $course->id]) }}">{{__('hier')}}</a>.

        @endif

    @endcomponent

@endsection
