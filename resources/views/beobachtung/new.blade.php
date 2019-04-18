@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Beobachtung erfassen')])

        @component('components.form', ['route' => ['beobachtung.store', ['kurs' => $kurs->id]]])

            @component('components.form.multiSelectInput', [
                'name' => 'tn_ids',
                'label' => __('TN'),
                'required' => true,
                'value' => $tn_id ?? '',
                'options' => $kurs->tns->all(),
                'valueFn' => function(\App\Models\TN $tn) { return $tn->id; },
                'displayFn' => function(\App\Models\TN $tn) { return $tn->pfadiname; },
                'multiple' => true,
            ])@endcomponent

            @component('components.form.textareaInput', ['name' => 'kommentar', 'label' => __('Beobachtung'), 'required' => true])@endcomponent

            <block-and-ma-input-wrapper v-slot="slotProps">

                @component('components.form.multiSelectInput', [
                    'name' => 'block_id',
                    'label' => __('Block'),
                    'required' => true,
                    'value' => $block_id ?? '',
                    'options' => $kurs->bloecke->all(),
                    'valueFn' => function(\App\Models\Block $block) { return $block->id; },
                    'displayFn' => function(\App\Models\Block $block) { return $block->blockname; },
                    'dataFn' => function(\App\Models\Block $block) { return '\'' . implode(',', array_map(function(\App\Models\MA $ma) { return $ma->id; }, $block->mas->all())) . '\''; },
                    'multiple' => false,
                    'onInput' => 'slotProps.onBlockUpdate',
                ])@endcomponent

                @component('components.form.multiSelectInput', [
                    'name' => 'ma_ids',
                    'label' => __('Mindestanforderungen'),
                    'valueBind' => 'slotProps.maValue',
                    'options' => $kurs->mas->all(),
                    'valueFn' => function(\App\Models\MA $ma) { return $ma->id; },
                    'displayFn' => function(\App\Models\MA $ma) { return $ma->anforderung; },
                    'multiple' => true,
                ])@endcomponent

            </block-and-ma-input-wrapper>

            @component('components.form.radioButtonInput', [
                'name' => 'bewertung',
                'label' => __('Bewertung'),
                'required' => true,
                'value' => '1',
                'options' => [ '2' => 'Positiv', '1' => 'Neutral', '0' => 'Negativ']
            ])@endcomponent

            @component('components.form.multiSelectInput', [
                'name' => 'qk_ids',
                'label' => __('Qualikategorien'),
                'options' => $kurs->qks->all(),
                'valueFn' => function(\App\Models\QK $qk) { return $qk->id; },
                'displayFn' => function(\App\Models\QK $qk) { return $qk->quali_kategorie; },
                'multiple' => true,
            ])@endcomponent

            @component('components.form.submit', ['label' => __('Speichern')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
