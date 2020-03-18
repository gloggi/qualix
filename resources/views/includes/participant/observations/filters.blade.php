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
