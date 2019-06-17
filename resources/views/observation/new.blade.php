@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Beobachtung erfassen')])

        @component('components.form', ['route' => ['observation.store', ['course' => $course->id]]])

            @component('components.form.multiSelectInput', [
                'name' => 'participant_ids',
                'label' => __('TN'),
                'required' => true,
                'value' => $participant_id ?? '',
                'options' => $course->participants->all(),
                'valueFn' => function(\App\Models\Participant $participant) { return $participant->id; },
                'displayFn' => function(\App\Models\Participant $participant) { return $participant->scout_name; },
                'multiple' => true,
            ])@endcomponent

            @component('components.form.textareaInput', ['name' => 'content', 'label' => __('Beobachtung'), 'required' => true])@endcomponent

            <block-and-ma-input-wrapper v-slot="slotProps">

                @component('components.form.multiSelectInput', [
                    'name' => 'block_id',
                    'label' => __('Block'),
                    'required' => true,
                    'value' => $block_id ?? '',
                    'options' => $course->blocks->all(),
                    'valueFn' => function(\App\Models\Block $block) { return $block->id; },
                    'displayFn' => function(\App\Models\Block $block) { return $block->blockname_and_number; },
                    'dataFn' => function(\App\Models\Block $block) { return '\'' . implode(',', array_map(function(\App\Models\Requirement $requirement) { return $requirement->id; }, $block->requirements->all())) . '\''; },
                    'multiple' => false,
                    'onInput' => 'slotProps.onBlockUpdate',
                ])@endcomponent

                @component('components.form.multiSelectInput', [
                    'name' => 'requirement_ids',
                    'label' => __('Mindestanforderungen'),
                    'valueBind' => 'slotProps.maValue',
                    'options' => $course->requirements->all(),
                    'valueFn' => function(\App\Models\Requirement $requirement) { return $requirement->id; },
                    'displayFn' => function(\App\Models\Requirement $requirement) { return $requirement->content; },
                    'multiple' => true,
                ])@endcomponent

            </block-and-ma-input-wrapper>

            @component('components.form.radioButtonInput', [
                'name' => 'impression',
                'label' => __('Eindruck'),
                'required' => true,
                'value' => '1',
                'options' => [ '2' => 'Positiv', '1' => 'Neutral', '0' => 'Negativ']
            ])@endcomponent

            @component('components.form.multiSelectInput', [
                'name' => 'category_ids',
                'label' => __('Kategorien'),
                'options' => $course->categories->all(),
                'valueFn' => function(\App\Models\Category $category) { return $category->id; },
                'displayFn' => function(\App\Models\Category $category) { return $category->name; },
                'multiple' => true,
            ])@endcomponent

            @component('components.form.submit', ['label' => __('Speichern')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
