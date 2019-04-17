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

            @component('components.form.multiSelectInput', [
                'name' => 'block_id',
                'label' => __('Block'),
                'required' => true,
                'value' => $block_id ?? '',
                'options' => $kurs->bloecke->all(),
                'valueFn' => function(\App\Models\Block $block) { return $block->id; },
                'displayFn' => function(\App\Models\Block $block) { return $block->blockname; },
                'multiple' => false,
            ])@endcomponent

            @component('components.form.textInput', ['name' => 'bewertung', 'label' => __('Bewertung'), 'required' => true, 'value' => '0'])@endcomponent

            @component('components.form.textInput', ['name' => 'kommentar', 'label' => __('Beobachtung'), 'required' => true])@endcomponent

            @component('components.form.submit', ['label' => __('Einladen')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
