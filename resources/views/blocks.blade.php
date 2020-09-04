@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.blocks.title')}}</template>

        @if (count($course->blocks))

            @php
                $days = [];
                foreach($course->blocks as $block) {
                    $days[$block->block_date->timestamp][] = $block;
                }
            @endphp

            @foreach($days as $day)
            <b-card no-body>
                <b-card-header v-b-toggle.collapse-{{ $day[0]->block_date->timestamp }}>
                    <h5 class="mb-0">
                        {{ $day[0]->block_date->formatLocalized('%A %d.%m.%Y') }}
                    </h5>
                </b-card-header>

                <b-collapse id="collapse-{{ $day[0]->block_date->timestamp }}" {{ ($course->archived || $day[0]->block_date->gt(\Carbon\Carbon::now()->subDays(2))) ? 'visible' : '' }}>
                    <b-list-group flush>
                        @foreach ($day as $block)
                            @if($course->archived)
                                <b-list-group-item tag="h5" class="mb-0">{{ $block->blockname_and_number }}</b-list-group-item>
                            @else
                                <b-list-group-item action tag="a" class="d-flex justify-content-between align-items-center" href="{{ route('observation.new', ['course' => $course->id, 'block' => $block->id]) }}">
                                    <h5 class="mb-0">{{ $block->blockname_and_number }}</h5>
                                    <span class="font-size-larger badge badge-primary">{{ count($block->observations) }} <i class="fas fa-binoculars"></i></span>
                                </b-list-group-item>
                            @endif
                        @endforeach
                    </b-list-group>
                </b-collapse>
            </b-card>
            @endforeach

        @else

            {{__('t.views.blocks.no_blocks', ['here' => $blockManagementLink])}}

        @endif

    </b-card>

@endsection
