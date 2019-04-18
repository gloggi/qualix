@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Block bearbeiten')])

        @component('components.form', ['route' => ['admin.block.update', ['kurs' => $kurs->id, 'block' => $block->id]]])

            @component('components.form.textInput', ['name' => 'full_block_number', 'label' => __('Blocknummer'), 'value' => $block->full_block_number])@endcomponent

            @component('components.form.textInput', ['name' => 'blockname', 'label' => __('Blockname'), 'required' => true, 'value' => $block->blockname])@endcomponent

            @component('components.form.dateInput', ['name' => 'datum', 'label' => __('Datum'), 'required' => true, 'value' => $block->datum])@endcomponent

            @component('components.form.multiSelectInput', [
                'name' => 'ma_ids',
                'label' => __('Mindestanforderungen'),
                'value' => implode(',', array_map(function(\App\Models\MA $ma) { return $ma->id; }, $block->mas->all())),
                'options' => $kurs->mas->all(),
                'valueFn' => function(\App\Models\MA $ma) { return $ma->id; },
                'displayFn' => function(\App\Models\MA $ma) { return $ma->anforderung; },
                'multiple' => true,
            ])@endcomponent

            @component('components.form.submit', ['label' => __('Speichern')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
