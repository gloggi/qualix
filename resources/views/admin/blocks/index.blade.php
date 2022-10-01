@extends('layouts.default')

@section('pagetitle'){{__('t.views.page_titles.admin_crib_overview') }}@endsection


@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.blocks.new')}}</template>

        <form-basic :action="['admin.block.store', { course: {{ $course->id }} }]">

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

        </form-basic>

    </b-card>

    <b-card>
        <template #header>{{__('t.views.admin.blocks.existing', ['courseName' => $course->name])}}</template>

        @if (count($course->blocks))

            @php
                $days = [];
                foreach($course->blocks->map->append('num_observations') as $block) {
                    $days[$block->block_date->timestamp][] = $block;
                }
                $blocks = [];
                foreach($days as $day) {
                    $blocks[] = ['type' => 'header', 'text' => $day[0]->block_date->formatLocalized(__('t.global.date_format'))];
                    $blocks = array_merge($blocks, $day);
                }
            @endphp
            <responsive-table
                :data="{{ json_encode($blocks) }}"
                :fields="[
                    { label: $t('t.models.block.full_block_number'), value: block => block.full_block_number },
                    { label: $t('t.models.block.name'), value: block => block.name },
                    @if(!$course->archived){ label: $t('t.models.block.num_observations'), value: block => block.num_observations },@endif
                ]"
                :actions="{
                    edit: block => routeUri('admin.block.edit', {course: {{ $course->id }}, block: block.id}),
                    delete: block => ({
                        text: $t('t.views.admin.blocks.really_delete', block) @if(!$course->archived) + ' ' + $tc('t.views.admin.blocks.observations_on_block', block.num_observations)@endif,
                        route: ['admin.block.delete', {course: {{ $course->id }}, block: block.id}]
                    })
                }"></responsive-table>

        @else

            {{__('t.views.admin.blocks.no_blocks')}}

            @component('components.help-text', ['key' => 't.views.admin.blocks.are_blocks_required', 'id' => 'noBlocksHelp'])@endcomponent

        @endif

    </b-card>

@endsection
