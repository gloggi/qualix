@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('t.views.participant_details.title'), 'bodyClass' => 'container-fluid'])

        <div class="row my-3">

            <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                <div class="square-container">
                    <img class="card-img-top img img-responsive full-width" src="{{ $participant->image_url != null ? asset(Storage::url($participant->image_url)) : asset('images/was-gaffsch.svg') }}" alt="{{ $participant->scout_name }}">
                </div>
            </div>

            <div class="col">
                <h3>{{ $participant->scout_name }}</h3>
                @if (isset($participant->group))<h5>{{ $participant->group }}</h5>@endif
                <p>{{ trans_choice('t.views.participant_details.num_observations', $participant->observations, ['positive' => $participant->positive->count(), 'neutral' => $participant->neutral->count(), 'negative' => $participant->negative->count()])}}</p>
                @php
                    $columns = [];
                    foreach ($course->users->all() as $user) {
                        $columns[$user->name] = function($observations) use($user) { return count(array_filter($observations, function(\App\Models\Observation $observation) use($user) {
                            return $observation->user->id === $user->id;
                        })); };
                    }
                @endphp
                @component('components.responsive-table', [
                    'data' => [$participant->observations->all()],
                    'fields' => $columns,
                ])@endcomponent
                <a href="{{ route('observation.new', ['course' => $course->id, 'participant' => $participant->id]) }}" class="btn btn-primary"><i class="fas fa-binoculars"></i> {{__('t.global.add_observation')}}</a>
            </div>

        </div>

    @endcomponent

    @component('components.card', ['header' => __('t.views.participant_details.existing_observations')])

        <div class="card">
            <div class="card-header" id="filters" data-toggle="collapse" data-target="#filters-collapse" aria-expanded="true" aria-controls="filters-collapse">
                <i class="fas fa-filter"></i> {{__('t.views.participant_details.filter')}}
            </div>

            <div id="filters-collapse" class="collapse{{ $requirement !== null || $category !== null ? ' show' : '' }}" aria-labelledby="filters">

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6 col-sm-12">

                            <form id="requirement-form" method="GET" action="{{ route('participants.detail', ['course' => $course->id, 'participant' => $participant->id]) }}#filters">

                                <multi-select
                                  id="requirement"
                                  name="requirement"
                                  class="form-control-multiselect"
                                  value="{{ $requirement }}"
                                  :allow-empty="true"
                                  placeholder="{{__('t.views.participant_details.filter_by_requirement')}}"
                                  @php
                                    $jsonOptions = $course->requirements->map(function (\App\Models\Requirement $requirement) {
                                        return [ 'label' => (string)$requirement->content, 'value' => (string)$requirement->id ];
                                    });
                                    $jsonOptions[] = [ 'label' => '-- ' . __('t.views.participant_details.observations_without_requirement') . ' --', 'value' => '0' ];
                                  @endphp
                                  :options="{{ json_encode($jsonOptions) }}"
                                  :multiple="false"
                                  :close-on-select="true"
                                  :show-labels="false"
                                  submit-on-input="requirement-form"
                                  :show-clear="true"></multi-select>

                            </form>

                        </div>

                        <div class="col-md-6 col-sm-12">

                            <form id="category-form" method="GET" action="{{ route('participants.detail', ['course' => $course->id, 'participant' => $participant->id]) }}#filters">

                                <multi-select
                                  id="category"
                                  name="category"
                                  class="form-control-multiselect"
                                  value="{{ $category }}"
                                  :allow-empty="true"
                                  placeholder="{{__('t.views.participant_details.filter_by_category')}}"
                                  @php
                                      $jsonOptions = $course->categories->map(function (App\Models\Category $category) {
                                          return [ 'label' => (string)$category->name, 'value' => (string)$category->id ];
                                      });
                                      $jsonOptions[] = [ 'label' => '-- ' . __('t.views.participant_details.observations_without_category') . ' --', 'value' => '0' ];
                                  @endphp
                                  :options="{{ json_encode($jsonOptions) }}"
                                  :multiple="false"
                                  :close-on-select="true"
                                  :show-labels="false"
                                  submit-on-input="category-form"
                                  :show-clear="true"></multi-select>

                            </form>

                        </div>

                    </div>
                </div>
            </div>
        </div>

        @if (count($observations))

            @component('components.responsive-table', [
                'data' => $observations,
                'rawColumns' => true,
                'fields' => [
                    __('t.models.observation.content') => function(\App\Models\Observation $observation) { return (new App\Util\HtmlString)->nl2br_e($observation->content); },
                    __('t.models.observation.block') => function(\App\Models\Observation $observation) { return $observation->block->blockname_and_number; },
                    __('t.models.observation.requirements') => function(\App\Models\Observation $observation) {
                        return (new App\Util\HtmlString)->s(implode('', array_map(function(\App\Models\Requirement $requirement) {
                            return (new App\Util\HtmlString)->s('<span class="badge badge-' . ($requirement->mandatory ? 'warning' : 'info') . '" style="white-space: normal">')->e($requirement->content)->s('</span>');
                        }, $observation->requirements->all())));
                    },
                    __('t.models.observation.impression') => function(\App\Models\Observation $observation) {
                        $impmression = $observation->impression;
                        if ($impmression === 0) return (new App\Util\HtmlString)->s('<span class="badge badge-danger">')->e(__('t.global.negative'))->s('</span>');
                        else if ($impmression === 2) return (new App\Util\HtmlString)->s('<span class="badge badge-success">')->e(__('t.global.positive'))->s('</span>');
                        else return (new App\Util\HtmlString)->s('<span class="badge badge-secondary">')->e(__('t.global.neutral'))->s('</span>');
                    },
                    __('t.models.observation.user') => function(\App\Models\Observation $observation) { return $observation->user->name; }
                ],
                'actions' => [
                    'edit' => function(\App\Models\Observation $observation) use ($course) { return route('observation.edit', ['course' => $course->id, 'observation' => $observation->id]); },
                    'delete' => function(\App\Models\Observation $observation) use ($course) { return [
                        'text' => __('t.views.participant_details.really_delete_observation'),
                        'route' => ['observation.delete', ['course' => $course->id, 'observation' => $observation->id]],
                     ];},
                ]
            ])@endcomponent

        @else

            {{__('t.views.participant_details.no_observations')}}

        @endif

    @endcomponent

@endsection
