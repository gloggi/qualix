@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Welche Mindestanforderungen können in den jeweiligen Blöcken beobachtet werden:')])

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
                                    @php $count_requirement= 0 @endphp
                                    @if(count($block->requirements))
                                        @foreach($block->requirements as $requirement)
                                            @if($requirement->mandatory)
                                                @if(!$count_requirement)
                                                    <br>
                                                    {{__('Killer: ')}}
                                                @endif
                                                <span class="badge badge-warning" style="white-space: normal"> {{$requirement->content}} </span>
                                                @php $count_requirement=$count_requirement+1  @endphp
                                            @endif
                                        @endforeach
                                        @if(!(count($block->requirements)==$count_requirement))
                                            <br>
                                            {{__('Nicht-Killer: ')}}
                                            @foreach($block->requirements as $requirement)
                                                @if(!$requirement->mandatory)
                                                    <span class="badge badge-info" style="white-space: normal"> {{$requirement->content}} </span>
                                                    @php $count_requirement=$count_requirement+1 @endphp
                                                @endif
                                            @endforeach
                                        @endif
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

            {{__('Bisher sind keine Blöcke erfasst. Bitte erfasse und verbinde sie')}} <a href="{{ route('admin.blocks', ['course' => $course->id]) }}">{{__('hier')}}</a>  {{__(' mit Mindestanforderungen')}}.

        @endif

    @endcomponent

@endsection
