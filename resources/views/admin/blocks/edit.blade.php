@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.blocks.edit')}}</template>

        @component('components.form', ['route' => ['admin.block.update', ['course' => $course->id, 'block' => $block->id]]])

            @component('components.form.textInput', ['name' => 'full_block_number', 'label' => __('t.models.block.full_block_number'), 'value' => $block->full_block_number])@endcomponent

            @component('components.form.textInput', ['name' => 'name', 'label' => __('t.models.block.name'), 'required' => true, 'autofocus' => true, 'value' => $block->name])@endcomponent

            @component('components.form.dateInput', ['name' => 'block_date', 'label' => __('t.models.block.block_date'), 'required' => true, 'value' => $block->block_date])@endcomponent

            @component('components.form.multiSelectInput', [
                'name' => 'requirements',
                'label' => __('t.models.block.requirements'),
                'value' => $block->requirements->all(),
                'options' => $course->requirements->all(),
                'valueFn' => function(\App\Models\Requirement $requirement) { return $requirement->id; },
                'displayFn' => function(\App\Models\Requirement $requirement) { return $requirement->content; },
                'multiple' => true,
            ])@endcomponent

            @component('components.form.submit', ['label' => __('t.global.save')])@endcomponent

        @endcomponent

    </b-card>

@endsection
