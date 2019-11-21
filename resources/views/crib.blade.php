@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('t.views.crib.title')])

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

                    <div id="collapse{{ $day[0]->block_date->timestamp }}" class="collapse{{ ($course->archived || $day[0]->block_date->gt(\Carbon\Carbon::now()->subDays(2))) ? ' show' : '' }}" aria-labelledby="heading{{ $day[0]->block_date->timestamp }}">
                        <ul class="list-group list-group-flush">
                            @foreach ($day as $block)
                                <h5 class="list-group-item mb-0">{{ $block->blockname_and_number }}
                                    @if(count($block->mandatory_requirements))
                                        <br>
                                        {{__('t.views.crib.mandatory_requirements')}}:
                                        @foreach($block->mandatory_requirements as $requirement)
                                            <span class="badge badge-warning" style="white-space: normal"> {{$requirement->content}} </span>
                                        @endforeach
                                    @endif
                                    @if(count($block->non_mandatory_requirements))
                                        <br>
                                        {{__('t.views.crib.non_mandatory_requirements')}}:
                                        @foreach($block->non_mandatory_requirements as $requirement)
                                            <span class="badge badge-info" style="white-space: normal"> {{$requirement->content}} </span>
                                        @endforeach
                                    @endif
                                </h5>

                            @endforeach
                        </ul>
                    </div>
                </div>
                @endforeach
            </div>
            @component('components.help-text', ['id' => 'noLinkedRequirementsHelp', 'key' => 't.views.crib.see_only_empty_blocks', 'params' => ['here' => $blockManagementLink]])@endcomponent

        @else

            {{__('t.views.crib.no_blocks', ['here' => $blockManagementLink])}}

        @endif

    @endcomponent

@endsection
