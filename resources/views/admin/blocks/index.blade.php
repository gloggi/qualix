@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.blocks.new')}}</template>

        @component('components.form', ['route' => ['admin.block.store', ['course' => $course->id]]])

            <input-text name="full_block_number" label="{{__('t.models.block.full_block_number')}}"></input-text>

            <input-text name="name" label="{{__('t.models.block.name')}}" required autofocus></input-text>

            <input-date name="block_date" value="{{ Auth::user()->getLastUsedBlockDate($course)->format('Y-m-d') }}" label="{{__('t.models.block.block_date')}}" required></input-date>

            <input-multi-select
                name="requirements"
                label="{{__('t.models.block.requirements')}}"
                :options="{{ json_encode($course->requirements->map->only('id', 'content')) }}"
                display-field="content"
                multiple></input-multi-select>

            <button-submit label="{{__('t.global.add')}}">

                <a class="btn btn-link mb-1" href="{{ route('admin.block.import', ['course' => $course]) }}">
                    {{ __('t.views.admin.blocks.import') }}
                </a>

                @component('components.help-text', ['key' => 't.views.admin.blocks.what_are_blocks', 'id' => 'blockHelp'])@endcomponent

            </button-submit>

        @endcomponent

    </b-card>

    <b-card>
        <template #header>{{__('t.views.admin.blocks.existing', ['courseName' => $course->name])}}</template>

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
                    __('t.models.block.full_block_number') => function(\App\Models\Block $block) { return $block->full_block_number; },
                    __('t.models.block.name') => function(\App\Models\Block $block) { return $block->name; },
                    __('t.models.block.num_observations') => function(\App\Models\Block $block) { return count($block->observations); },
                ];
                if ($course->archived) {
                    unset($fields[__('t.models.block.num_observations')]);
                }
            @endphp
            @component('components.responsive-table', [
                'data' => $blocks,
                'fields' => $fields,
                'actions' => [
                    'edit' => function(\App\Models\Block $block) use ($course) { return route('admin.block.edit', ['course' => $course->id, 'block' => $block->id]); },
                    'delete' => function(\App\Models\Block $block) use ($course) { return [
                        'text' => __('t.views.admin.blocks.really_delete', ['name' => $block->name]) . ($course->archived ? '' : ' ' . trans_choice('t.views.admin.blocks.observations_on_block', $block->observations)),
                        'route' => ['admin.block.delete', ['course' => $course->id, 'block' => $block->id]],
                     ];},
                ]
            ])@endcomponent

        @else

            {{__('t.views.admin.blocks.no_blocks')}}

            @component('components.help-text', ['key' => 't.views.admin.blocks.are_blocks_required', 'id' => 'noBlocksHelp'])@endcomponent

        @endif

    </b-card>

@endsection
