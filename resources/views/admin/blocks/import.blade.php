@extends('layouts.default')

@section('pagetitle'){{__('t.views.admin.block_import.page_title') }}@endsection

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.block_import.import_from', ['source' => __('t.views.admin.block_import.ecamp2.name')])}}</template>

        <form-basic :action="['admin.block.import', { course: {{ $course->id }} }]" enctype="multipart/form-data">

            <input-file name="file" label="{{__('t.views.admin.block_import.ecamp2.block_overview')}}" required accept="application/vnd.ms-excel"></input-file>

            <input-hidden name="source" value="eCamp2BlockOverview"></input-hidden>

            <button-submit label="{{__('t.views.admin.block_import.import')}}">

                @component('components.help-text', ['key' => 't.views.admin.block_import.ecamp2.how_to_get_the_block_overview', 'id' => 'eCamp2BlockOverviewHelp', 'params' => ['ecamp2' => $ecamp2Link]])
                    <img src="{{ Vite::asset('resources/images/ecamp2-block-overview.png') }}" class="img-fluid w-100 mt-2 border">
                @endcomponent

            </button-submit>

        </form-basic>

    </b-card>

@endsection
