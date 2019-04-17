@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Beobachtung erfassen')])

        @component('components.form', ['route' => ['beobachtung.store', ['kurs' => $kurs->id]]])

            @component('components.form.textInput', ['name' => 'tn_ids', 'label' => __('TN-Ids'), 'required' => true, 'value' => $tn_id ?? ''])@endcomponent

            @component('components.form.textInput', ['name' => 'block_id', 'label' => __('Block-Id'), 'required' => true, 'value' => $block_id ?? ''])@endcomponent

            @component('components.form.textInput', ['name' => 'bewertung', 'label' => __('Bewertung'), 'required' => true, 'value' => '0'])@endcomponent

            @component('components.form.textInput', ['name' => 'kommentar', 'label' => __('Beobachtung'), 'required' => true])@endcomponent

            @component('components.form.submit', ['label' => __('Einladen')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
