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
                                <b-list-group horizontal class="row  m-0">
                                    <b-list-group-item tag="h5" class="border-0  mb-0 col-sm-12 col-md-6">
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
                                    <b-list-group-item tag="h5" class="border-0 mb-0 col-sm-12 col-md-6">
                                        @if(isset($trainerObservationAssignments[$block->id]))
                                            <div class="row">
                                                @foreach($trainerObservationAssignments[$block->id] as $participant)
                                                    <div class="col-6 col-sm-4 col-md-4 col-lg-3 mb-1 text-center">
                                                        <a href="{{ route('observation.new', ['course' => $course->id, 'participant' => $participant->id, 'block' => $block->id, 'crib' => true]) }}">
                                                            <div class="card rounded-circle mb-0 position-relative">
                                                                <img class="card-img-top img img-responsive full-width" src="{{ $participant->image_url != null ? asset(Storage::url($participant->image_url)) : asset('images/was-gaffsch.svg') }}" alt="{{ $participant->scout_name }}">
                                                                <div class="card-img-overlay w-100 p-0 d-flex flex-column ">
                                                                    <b-button variant="{{$participant->observation_count >= $neededObservations ? 'outline-success' : 'outline-danger'}}" class="bg-white py-0 mt-1 w-100 mt-auto text-overflow-ellipsis">{{$participant->scout_name}}</b-button>
                                                                </div>
                                                            </div>

                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </b-list-group-item>

                                </b-list-group>

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
