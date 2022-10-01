@extends('layouts.default')

@section('pagetitle'){{__('t.views.page_titles.crib_overview')}}@endsection

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

            @if($showObservationAssignments)
                <div class="d-flex justify-content-end mb-2">
                    <label for="user" class="col-form-label text-md-right mr-2">{{ __('t.views.crib.view_as') }}</label>
                    <multi-select
                        name="user"
                        :value="{{ json_encode("$userId") }}"
                        class=""
                        required
                        :options="{{ json_encode($course->users->map->only('id', 'name')) }}"
                        display-field="name"
                        @update:selected="selected => $window.location = routeUri('crib', {course: {{ $course->id }}, user: selected.id})"></multi-select>
                </div>
            @endif

            @foreach($days as $day)
            <b-card no-body>
                <b-card-header v-b-toggle.collapse-{{ $day[0]->block_date->timestamp }}>
                    <h5 class="mb-0">
                        {{ $day[0]->block_date->formatLocalized(__('t.global.date_format')) }}
                    </h5>
                </b-card-header>

                <b-collapse id="collapse-{{ $day[0]->block_date->timestamp }}" {{ ($course->archived || $day[0]->block_date->gt(\Carbon\Carbon::now()->subDays(2))) ? 'visible' : '' }}>
                    <b-list-group flush>
                        @foreach ($day as $block)
                            <b-list-group-item class="mb-0 p-0">
                                <b-list-group horizontal class="row m-0">
                                    <b-list-group-item :href="routeUri('observation.new', {course: {{ $course->id }}, block: {{ $block->id }} })" class="border-0 mb-0 col-12 @if($showObservationAssignments && isset($trainerObservationAssignments[$block->id]))col-md-6 @endif">
                                        <h5>{{ $block->blockname_and_number }}</h5>
                                        @if(count($block->mandatory_requirements))
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
                                    @if($showObservationAssignments && isset($trainerObservationAssignments[$block->id]))
                                        <b-list-group-item class="border-0 mb-0 col-sm-12 col-md-6">
                                                <div class="row">
                                                    @foreach($trainerObservationAssignments[$block->id] as $participant)
                                                        <div class="col-4 col-sm-3 col-md-4 col-lg-3 mb-1 text-center" v-b-tooltip.hover title="{{$participant->observation_assignment_names}}">
                                                            <a href="{{ route('observation.new', ['course' => $course->id, 'participant' => $participant->id, 'block' => $block->id]) }}">
                                                                <div class="mb-0 position-relative">
                                                                    <img class="card-img-top rounded-circle img img-responsive full-width" src="{{ $participant->image_url != null ? asset(Storage::url($participant->image_url)) : asset('images/was-gaffsch.svg') }}" alt="{{ $participant->scout_name }}">
                                                                    <div class="card-img-overlay w-100 p-0 d-flex flex-column ">
                                                                        <b-badge v-if="{{ $participant->observation_count }}" variant="primary" class="ml-auto mb-auto font-size-larger" pill>{{ $participant->observation_count }}</b-badge>
                                                                        <b-badge v-else variant="danger" class="ml-auto mb-auto font-size-larger" pill>{{ $participant->observation_count }}</b-badge>
                                                                    </div>
                                                                    <p class="text-overflow-ellipsis">{{ $participant->scout_name }}</p>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                </div>
                                        </b-list-group-item>
                                    @endif

                                </b-list-group>

                            </b-list-group-item>

                        @endforeach
                    </b-list-group>
                </b-collapse>
            </b-card>
            @endforeach

            @if($course->uses_requirements)
                @component('components.help-text', ['id' => 'noLinkedRequirementsHelp', 'key' => 't.views.crib.see_only_empty_blocks', 'params' => ['here' => $blockManagementLink]])@endcomponent
            @endif

        @else

            {{__('t.views.crib.no_blocks', ['here' => $blockManagementLink])}}

        @endif

    </b-card>

@endsection
