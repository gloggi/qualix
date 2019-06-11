@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Block bearbeiten')])

        @component('components.form', ['route' => ['admin.block.update', ['course' => $course->id, 'block' => $block->id]]])

            @component('components.form.textInput', ['name' => 'full_block_number', 'label' => __('Blocknummer'), 'value' => $block->full_block_number])@endcomponent

            @component('components.form.textInput', ['name' => 'name', 'label' => __('Blockname'), 'required' => true, 'value' => $block->name])@endcomponent

            @component('components.form.dateInput', ['name' => 'block_date', 'label' => __('Datum'), 'required' => true, 'value' => $block->block_date])@endcomponent

            @component('components.form.multiSelectInput', [
                'name' => 'requirement_ids',
                'label' => __('Mindestanforderungen'),
                'value' => implode(',', array_map(function(\App\Models\Requirement $requirement) { return $requirement->id; }, $block->requirements->all())),
                'options' => $course->requirements->all(),
                'valueFn' => function(\App\Models\Requirement $requirement) { return $requirement->id; },
                'displayFn' => function(\App\Models\Requirement $requirement) { return $requirement->content; },
                'multiple' => true,
            ])@endcomponent

            @component('components.form.submit', ['label' => __('Speichern')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
