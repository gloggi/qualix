@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.crib.title')}}</template>

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
                            <b-list-group-item tag="h5" class="mb-0">
                                {{ $block->blockname_and_number }}
                                @if(count($block->mandatory_requirements))
                                    <br>
                                    {{__('t.views.crib.mandatory_requirements')}}:
                                    @foreach($block->mandatory_requirements as $requirement)
                                        <span class="white-space-normal badge badge-warning"> {{$requirement->content}} </span>
                                    @endforeach
                                @endif
                                @if(count($block->non_mandatory_requirements))
                                    <br>
                                    {{__('t.views.crib.non_mandatory_requirements')}}:
                                    @foreach($block->non_mandatory_requirements as $requirement)
                                        <span class="white-space-normal badge badge-info"> {{$requirement->content}} </span>
                                    @endforeach
                                @endif
                            </b-list-group-item>

                        @endforeach
                    </b-list-group>
                </b-collapse>
            </b-card>
            @endforeach

            @component('components.help-text', ['id' => 'noLinkedRequirementsHelp', 'key' => 't.views.crib.see_only_empty_blocks', 'params' => ['here' => $blockManagementLink]])@endcomponent

        @else

            {{__('t.views.crib.no_blocks', ['here' => $blockManagementLink])}}

        @endif

    </b-card>

@endsection
