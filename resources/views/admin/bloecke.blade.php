@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Blöcke :courseName', ['courseName' => $kurs->name])])

        @if (count($kurs->bloecke))

            @php
                $days = [];
                foreach($kurs->bloecke as $block) {
                    $days[$block->datum->timestamp][] = $block;
                }
                $bloecke = [];
                foreach($days as $day) {
                    $bloecke[] = ['type' => 'header', 'text' => $day[0]->datum->formatLocalized('%A %d.%m.%Y')];
                    $bloecke = array_merge($bloecke, $day);
                }
            @endphp
            @component('components.responsive-table', [
                'data' => $bloecke,
                'fields' => [
                    __('Blocknummer') => function(\App\Models\Block $block) { return $block->full_block_number; },
                    __('Blockname') => function(\App\Models\Block $block) { return $block->blockname; },
                    __('Anzahl Beobachtungen') => function(\App\Models\Block $block) { return count($block->beobachtungen); },
                ],
                'actions' => [
                    'edit' => function(\App\Models\Block $block) use ($kurs) { return route('admin.block.edit', ['kurs' => $kurs->id, 'block' => $block->id]); },
                    'delete' => function(\App\Models\Block $block) use ($kurs) { return [
                        'text' => __('Willst du diesen Block wirklich löschen? ' . count($block->beobachtungen) . ' Beobachtung(en) ist / sind darauf zugewiesen.'),
                        'route' => ['admin.block.delete', ['kurs' => $kurs->id, 'block' => $block->id]],
                     ];},
                ]
            ])@endcomponent

        @else

            {{__('Bisher sind keine Blöcke erfasst.')}}

        @endif

    @endcomponent

    @component('components.card', ['header' => __('Neuer Block')])

        @component('components.form', ['route' => ['admin.block.store', ['kurs' => $kurs->id]]])

            @component('components.form.textInput', ['name' => 'full_block_number', 'label' => __('Blocknummer')])@endcomponent

            @component('components.form.textInput', ['name' => 'blockname', 'label' => __('Blockname'), 'required' => true])@endcomponent

            @component('components.form.textInput', ['name' => 'datum', 'label' => __('Datum'), 'required' => true])@endcomponent

            @component('components.form.submit', ['label' => __('Hinzufügen')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
