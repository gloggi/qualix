@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('observation.edit')])

        @component('components.form', ['route' => ['observation.update', ['course' => $course->id, 'observation' => $observation->id]]])

            @component('components.form.multiSelectInput', [
                'name' => 'participant_id',
                'label' => __('t.models.observation.participant'),
                'required' => true,
                'value' => $observation->participant->id,
                'options' => $course->participants->all(),
                'valueFn' => function(\App\Models\Participant $participant) { return $participant->id; },
                'displayFn' => function(\App\Models\Participant $participant) { return $participant->scout_name; },
                'multiple' => false,
                'disabled' => true,
            ])@endcomponent

            @component('components.form.textareaInput', ['name' => 'content', 'label' => __('t.models.observation.content'), 'required' => true, 'autofocus' => true, 'value' => $observation->content])@endcomponent

            @component('components.form.multiSelectInput', [
                'name' => 'block_id',
                'label' => __('t.models.observation.block'),
                'required' => true,
                'value' => $observation->block->id,
                'options' => $course->blocks->all(),
                'valueFn' => function(\App\Models\Block $block) { return $block->id; },
                'displayFn' => function(\App\Models\Block $block) { return $block->blockname_and_number; },
                'dataFn' => function(\App\Models\Block $block) { return implode(',', array_map(function(\App\Models\Requirement $requirement) { return $requirement->id; }, $block->requirements->all())); },
                'multiple' => false,
            ])@endcomponent

            @component('components.form.multiSelectInput', [
                'name' => 'requirement_ids',
                'label' => __('t.models.observation.requirements'),
                'value' => implode(',', array_map(function (\App\Models\Requirement $requirement) { return $requirement->id; }, $observation->requirements->all())),
                'options' => $course->requirements->all(),
                'valueFn' => function(\App\Models\Requirement $requirement) { return $requirement->id; },
                'displayFn' => function(\App\Models\Requirement $requirement) { return $requirement->content; },
                'multiple' => true,
            ])@endcomponent

            @component('components.form.radioButtonInput', [
                'name' => 'impression',
                'label' => __('t.models.observation.impression'),
                'required' => true,
                'value' => $observation->impression,
                'options' => [ '2' => __('t.global.positive'), '1' => __('t.global.neutral'), '0' => __('t.global.negative')]
            ])@endcomponent

            @component('components.form.multiSelectInput', [
                'name' => 'category_ids',
                'label' => __('t.models.observation.categories'),
                'options' => $course->categories->all(),
                'value' => implode(',', array_map(function (\App\Models\Category $category) { return $category->id; }, $observation->categories->all())),
                'valueFn' => function(\App\Models\Category $category) { return $category->id; },
                'displayFn' => function(\App\Models\Category $category) { return $category->name; },
                'multiple' => true,
            ])@endcomponent

            @component('components.form.submit', ['label' => __('t.global.save')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
