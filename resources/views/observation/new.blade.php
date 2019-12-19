@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('t.views.observations.new')])

        @component('components.form', ['route' => ['observation.store', ['course' => $course->id]]])

            @component('components.form.multiSelectInput', [
                'name' => 'participant_ids',
                'label' => __('t.models.observation.participant'),
                'required' => true,
                'value' => $participant_id,
                'options' => $course->participants->all(),
                'valueFn' => function(\App\Models\Participant $participant) { return $participant->id; },
                'displayFn' => function(\App\Models\Participant $participant) { return $participant->scout_name; },
                'multiple' => true,
                'autofocus' => ($participant_id === null)
            ])@endcomponent

            @component('components.form.textareaInput', ['name' => 'content', 'label' => __('t.models.observation.content'), 'required' => true, 'autofocus' => ($participant_id !== null)])@endcomponent

            <block-and-requirements-input-wrapper v-slot="slotProps" @if(old('requirement_ids', 'OBSERVATION_NO_OLD_VALUES') !== 'OBSERVATION_NO_OLD_VALUES') :has-old-values="true" @endif>

                @component('components.form.multiSelectInput', [
                    'name' => 'block_id',
                    'label' => __('t.models.observation.block'),
                    'required' => true,
                    'value' => $block_id,
                    'options' => $course->blocks->all(),
                    'valueFn' => function(\App\Models\Block $block) { return $block->id; },
                    'displayFn' => function(\App\Models\Block $block) { return $block->blockname_and_number; },
                    'dataFn' => function(\App\Models\Block $block) { return implode(',', array_map(function(\App\Models\Requirement $requirement) { return $requirement->id; }, $block->requirements->all())); },
                    'multiple' => false,
                    'onInput' => 'slotProps.onBlockUpdate',
                ])@endcomponent

                @component('components.form.multiSelectInput', [
                    'name' => 'requirement_ids',
                    'label' => __('t.models.observation.requirements'),
                    'valueBind' => 'slotProps.requirementsValue',
                    'options' => $course->requirements->all(),
                    'valueFn' => function(\App\Models\Requirement $requirement) { return $requirement->id; },
                    'displayFn' => function(\App\Models\Requirement $requirement) { return $requirement->content; },
                    'multiple' => true,
                ])@endcomponent

            </block-and-requirements-input-wrapper>

            @component('components.form.radioButtonInput', [
                'name' => 'impression',
                'label' => __('t.models.observation.impression'),
                'required' => true,
                'value' => '1',
                'options' => [ '2' => __('t.global.positive'), '1' => __('t.global.neutral'), '0' => __('t.global.negative')]
            ])@endcomponent

            @component('components.form.multiSelectInput', [
                'name' => 'category_ids',
                'label' => __('t.models.observation.categories'),
                'options' => $course->categories->all(),
                'valueFn' => function(\App\Models\Category $category) { return $category->id; },
                'displayFn' => function(\App\Models\Category $category) { return $category->name; },
                'multiple' => true,
            ])@endcomponent

            @component('components.form.submit', ['label' => __('t.global.save')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
