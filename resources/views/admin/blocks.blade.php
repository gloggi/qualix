@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Neuer Block')])

        @component('components.form', ['route' => ['admin.block.store', ['course' => $course->id]]])

            @component('components.form.textInput', ['name' => 'full_block_number', 'label' => __('Blocknummer')])@endcomponent

            @component('components.form.textInput', ['name' => 'name', 'label' => __('Blockname'), 'required' => true, 'autofocus' => true])@endcomponent

            @component('components.form.dateInput', ['name' => 'block_date', 'label' => __('Datum'), 'required' => true, 'value' => Auth::user()->getLastUsedBlockDate($course)])@endcomponent

            @component('components.form.multiSelectInput', [
                'name' => 'requirement_ids',
                'label' => __('Mindestanforderungen'),
                'options' => $course->requirements->all(),
                'valueFn' => function(\App\Models\Requirement $requirement) { return $requirement->id; },
                'displayFn' => function(\App\Models\Requirement $requirement) { return $requirement->content; },
                'multiple' => true,
            ])@endcomponent

            @component('components.form.submit', ['label' => __('Hinzufügen')])@endcomponent

        @endcomponent

    @endcomponent

    @component('components.card', ['header' => __('Blöcke :courseName', ['courseName' => $course->name])])

        @if (count($course->blocks))

            @php
                $days = [];
                foreach($course->blocks as $block) {
                    $days[$block->block_date->timestamp][] = $block;
                }
                $blocks = [];
                foreach($days as $day) {
                    $blocks[] = ['type' => 'header', 'text' => $day[0]->block_date->formatLocalized('%A %d.%m.%Y')];
                    $blocks = array_merge($blocks, $day);
                }
                $fields = [
                    __('Blocknummer') => function(\App\Models\Block $block) { return $block->full_block_number; },
                    __('Blockname') => function(\App\Models\Block $block) { return $block->name; },
                    __('Anzahl Beobachtungen') => function(\App\Models\Block $block) { return count($block->observations); },
                ];
                if ($course->archived) {
                    unset($fields[__('Anzahl Beobachtungen')]);
                }
            @endphp
            @component('components.responsive-table', [
                'data' => $blocks,
                'fields' => $fields,
                'actions' => [
                    'edit' => function(\App\Models\Block $block) use ($course) { return route('admin.block.edit', ['course' => $course->id, 'block' => $block->id]); },
                    'delete' => function(\App\Models\Block $block) use ($course) { return [
                        'text' => __('Willst du diesen Block wirklich löschen?' . ($course->archived ? '' : ' ' . count($block->observations) . ' Beobachtung(en) ist / sind darauf zugewiesen.')),
                        'route' => ['admin.block.delete', ['course' => $course->id, 'block' => $block->id]],
                     ];},
                ]
            ])@endcomponent

        @else

            {{__('Bisher sind keine Blöcke erfasst.')}}

        @endif

    @endcomponent

@endsection
