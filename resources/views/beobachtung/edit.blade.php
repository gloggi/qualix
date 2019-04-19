@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Beobachtung bearbeiten')])

        @component('components.form', ['route' => ['beobachtung.update', ['kurs' => $kurs->id, 'beobachtung' => $beobachtung->id]]])

            @component('components.form.multiSelectInput', [
                'name' => 'tn_ids',
                'label' => __('TN'),
                'required' => true,
                'value' => $beobachtung->tn->id,
                'options' => $kurs->tns->all(),
                'valueFn' => function(\App\Models\TN $tn) { return $tn->id; },
                'displayFn' => function(\App\Models\TN $tn) { return $tn->pfadiname; },
                'multiple' => true,
                'disabled' => true,
            ])@endcomponent

            @component('components.form.textareaInput', ['name' => 'kommentar', 'label' => __('Beobachtung'), 'required' => true, 'value' => $beobachtung->kommentar])@endcomponent

            @component('components.form.multiSelectInput', [
                'name' => 'block_id',
                'label' => __('Block'),
                'required' => true,
                'value' => $beobachtung->block->id,
                'options' => $kurs->bloecke->all(),
                'valueFn' => function(\App\Models\Block $block) { return $block->id; },
                'displayFn' => function(\App\Models\Block $block) { return $block->blockname_and_number; },
                'dataFn' => function(\App\Models\Block $block) { return '\'' . implode(',', array_map(function(\App\Models\MA $ma) { return $ma->id; }, $block->mas->all())) . '\''; },
                'multiple' => false,
            ])@endcomponent

            @component('components.form.multiSelectInput', [
                'name' => 'ma_ids',
                'label' => __('Mindestanforderungen'),
                'value' => implode(',', array_map(function (\App\Models\MA $ma) { return $ma->id; }, $beobachtung->mas->all())),
                'options' => $kurs->mas->all(),
                'valueFn' => function(\App\Models\MA $ma) { return $ma->id; },
                'displayFn' => function(\App\Models\MA $ma) { return $ma->anforderung; },
                'multiple' => true,
            ])@endcomponent

            @component('components.form.radioButtonInput', [
                'name' => 'bewertung',
                'label' => __('Bewertung'),
                'required' => true,
                'value' => $beobachtung->bewertung,
                'options' => [ '2' => 'Positiv', '1' => 'Neutral', '0' => 'Negativ']
            ])@endcomponent

            @component('components.form.multiSelectInput', [
                'name' => 'qk_ids',
                'label' => __('Qualikategorien'),
                'options' => $kurs->qks->all(),
                'value' => implode(',', array_map(function (\App\Models\QK $qk) { return $qk->id; }, $beobachtung->qks->all())),
                'valueFn' => function(\App\Models\QK $qk) { return $qk->id; },
                'displayFn' => function(\App\Models\QK $qk) { return $qk->quali_kategorie; },
                'multiple' => true,
            ])@endcomponent

            @component('components.form.submit', ['label' => __('Speichern')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
