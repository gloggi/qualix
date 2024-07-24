@extends('layouts.default')

@section('pagetitle'){{__('t.views.observations.page_title') }}@endsection


@section('content')

    <b-card>
        <template #header>{{__('t.views.observations.new')}}</template>

        <form-basic :action="['observation.store', { course: {{ $course->id }} }]">

            <input-multi-select
                name="participants"
                value="{{ $participants }}"
                label="{{__('t.models.observation.participants')}}"
                :options="{{ json_encode($course->participants->map->only('id', 'scout_name')) }}"
                :groups="{{json_encode(
                    $course->participantGroups->mapWithKeys(function ($group) {
                        return [$group['group_name'] => $group->participants->pluck('id')->join(',')];
                    }), JSON_FORCE_OBJECT)}}"
                display-field="scout_name"
                multiple
                required
                :autofocus="{{ $participants === null ? 'true' : 'false' }}"></input-multi-select>

            <input-textarea
                name="content"
                label="{{__('t.models.observation.content')}}"
                required
                :autofocus="{{ ($participants !== null) ? 'true' : 'false' }}"
                :limit="{{App\Models\Observation::CHAR_LIMIT}}"
                v-slot="slotProps">
              <char-limit :current-value="slotProps.currentValue" :limit="slotProps.limit"></char-limit>
            </input-textarea>

            <block-and-requirements-input-wrapper
                v-slot="{ onBlockUpdate, requirementsValue }"
                initial-requirements-value="{{ old('requirements') }}"
                :block-requirements-mapping="{{ json_encode($course->blocks->map->only('id', 'requirement_ids')) }}">

                <input-multi-select
                    name="block"
                    value="{{ $block }}"
                    label="{{__('t.models.observation.block')}}"
                    required
                    :options="{{ json_encode($blocks->map->only('id', 'blockname_and_number', 'requirement_ids')) }}"
                    display-field="blockname_and_number"
                    @input="onBlockUpdate">
                    <template #below="{ value }">
                        <button-new-evaluation-grid
                            :course-id="{{ $course->id }}"
                            :block-id="value"
                            :evaluation-grid-templates-mapping="{{ json_encode($course->evaluationGridTemplatesPerBlock()) }}">
                            {{__('t.views.observations.evaluation_grid_templates_available')}}
                        </button-new-evaluation-grid>
                    </template>
                </input-multi-select>

                @if($course->uses_requirements)
                    <input-multi-select
                        name="requirements"
                        :value="requirementsValue"
                        error-message="{{ $errors->first('requirements') }}"
                        label="{{__('t.models.observation.requirements')}}"
                        :options="{{ json_encode($course->requirements->map->only('id', 'content')) }}"
                        display-field="content"
                        multiple></input-multi-select>
                @endif

            </block-and-requirements-input-wrapper>

            @if($course->uses_impressions)
                <input-radio-button
                    name="impression"
                    value="1"
                    label="{{__('t.models.observation.impression')}}"
                    required
                    :options="{{ json_encode([ '2' => __('t.global.positive'), '1' => __('t.global.neutral'), '0' => __('t.global.negative')]) }}"></input-radio-button>
            @endif

            @if($course->uses_categories)
                <input-multi-select
                    name="categories"
                    label="{{__('t.models.observation.categories')}}"
                    :options="{{ json_encode($course->categories->map->only('id', 'name')) }}"
                    display-field="name"
                    multiple></input-multi-select>
            @endif

            <button-submit></button-submit>

        </form-basic>

    </b-card>

@endsection

