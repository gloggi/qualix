@extends('layouts.default')

@section('pagetitle'){{__('t.views.page_titles.edit_observation') }}@endsection

@section('content')

    <b-card>
        <template #header>{{__('t.views.observations.edit')}}</template>

        <form-basic :action="['observation.update', { course: {{ $course->id }}, observation: {{ $observation->id }} }]">

            <input-multi-select
                name="participants"
                value="{{ $observation->participants->pluck('id')->join(',') }}"
                label="{{__('t.models.observation.participants')}}"
                required
                :options="{{ json_encode($course->participants->map->only('id', 'scout_name')) }}"
                :groups="{{json_encode(
                        $course->participantGroups->mapWithKeys(function ($group) {
                            return [$group['group_name'] => $group->participants->pluck('id')->join(',')];
                        }), JSON_FORCE_OBJECT)}}"
                display-field="scout_name"
                multiple></input-multi-select>

            <input-textarea
                name="content"
                value="{{ $observation->content }}"
                label="{{__('t.models.observation.content')}}"
                required
                autofocus
                :limit="{{App\Models\Observation::CHAR_LIMIT}}"
                v-slot="slotProps">
                <char-limit :current-value="slotProps.currentValue" :limit="slotProps.limit"></char-limit>
            </input-textarea>

            <input-multi-select
                name="block"
                value="{{ $observation->block->id }}"
                label="{{__('t.models.observation.block')}}"
                required
                :options="{{ json_encode($course->blocks->map->only('id', 'blockname_and_number')) }}"
                display-field="blockname_and_number"></input-multi-select>

            @if($course->uses_requirements)
                <input-multi-select
                    name="requirements"
                    value="{{ $observation->requirements->pluck('id')->join(',') }}"
                    label="{{__('t.models.observation.requirements')}}"
                    :options="{{ json_encode($course->requirements->map->only('id', 'content')) }}"
                    display-field="content"
                    multiple></input-multi-select>
            @endif

            @if($course->uses_impressions)
                <input-radio-button
                    name="impression"
                    value="{{ $observation->impression }}"
                    label="{{__('t.models.observation.impression')}}"
                    required
                    :options="{{ json_encode([ '2' => __('t.global.positive'), '1' => __('t.global.neutral'), '0' => __('t.global.negative')]) }}"></input-radio-button>
            @endif

            @if($course->uses_categories)
                <input-multi-select
                    name="categories"
                    value="{{ $observation->categories->pluck('id')->join(',') }}"
                    label="{{__('t.models.observation.categories')}}"
                    :options="{{ json_encode($course->categories->map->only('id', 'name')) }}"
                    display-field="name"
                    multiple></input-multi-select>
            @endif

            <button-submit></button-submit>

        </form-basic>

    </b-card>

@endsection
