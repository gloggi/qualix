@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Beobachtung bearbeiten')])

        @component('components.form', ['route' => ['observation.update', ['course' => $course->id, 'observation' => $observation->id]]])

            @component('components.form.multiSelectInput', [
                'name' => 'participant_id',
                'label' => __('TN'),
                'required' => true,
                'value' => $observation->participant->id,
                'options' => $course->participants->all(),
                'valueFn' => function(\App\Models\Participant $participant) { return $participant->id; },
                'displayFn' => function(\App\Models\Participant $participant) { return $participant->scout_name; },
                'multiple' => false,
                'disabled' => true,
            ])@endcomponent

            @component('components.form.textareaInput', ['name' => 'content', 'label' => __('Beobachtung'), 'required' => true, 'autofocus' => true, 'value' => $observation->content])@endcomponent

            @component('components.form.multiSelectInput', [
                'name' => 'block_id',
                'label' => __('Block'),
                'required' => true,
                'value' => $observation->block->id,
                'options' => $course->blocks->all(),
                'valueFn' => function(\App\Models\Block $block) { return $block->id; },
                'displayFn' => function(\App\Models\Block $block) { return $block->blockname_and_number; },
                'dataFn' => function(\App\Models\Block $block) { return '\'' . implode(',', array_map(function(\App\Models\Requirement $ma) { return $ma->id; }, $block->requirements->all())) . '\''; },
                'multiple' => false,
            ])@endcomponent

            @component('components.form.multiSelectInput', [
                'name' => 'requirement_ids',
                'label' => __('Mindestanforderungen'),
                'value' => implode(',', array_map(function (\App\Models\Requirement $ma) { return $ma->id; }, $observation->requirements->all())),
                'options' => $course->requirements->all(),
                'valueFn' => function(\App\Models\Requirement $ma) { return $ma->id; },
                'displayFn' => function(\App\Models\Requirement $ma) { return $ma->content; },
                'multiple' => true,
            ])@endcomponent

            @component('components.form.radioButtonInput', [
                'name' => 'impression',
                'label' => __('Eindruck'),
                'required' => true,
                'value' => $observation->impression,
                'options' => [ '2' => 'Positiv', '1' => 'Neutral', '0' => 'Negativ']
            ])@endcomponent

            @component('components.form.multiSelectInput', [
                'name' => 'category_ids',
                'label' => __('Kategorien'),
                'options' => $course->categories->all(),
                'value' => implode(',', array_map(function (\App\Models\Category $category) { return $category->id; }, $observation->categories->all())),
                'valueFn' => function(\App\Models\Category $category) { return $category->id; },
                'displayFn' => function(\App\Models\Category $category) { return $category->name; },
                'multiple' => true,
            ])@endcomponent

            @component('components.form.submit', ['label' => __('Speichern')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
