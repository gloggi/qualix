@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Block bearbeiten')])

        @component('components.form', ['route' => ['admin.block.update', ['kurs' => $kurs->id, 'block' => $block->id]]])

            @component('components.form.textInput', ['name' => 'full_block_number', 'label' => __('Blocknummer'), 'value' => $block->full_block_number])@endcomponent

            @component('components.form.textInput', ['name' => 'blockname', 'label' => __('Blockname'), 'required' => true, 'value' => $block->blockname])@endcomponent

            @component('components.form.textInput', ['name' => 'datum', 'label' => __('Datum'), 'required' => true, 'value' => $block->datum->format('d.m.Y')])@endcomponent

            @component('components.form.submit', ['label' => __('Speichern')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
