@extends('layouts.default')

@section('pagetitle'){{__('t.views.admin.block_import.page_title') }}@endsection

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.block_import.import_from', ['source' => __('t.views.admin.block_import.ecamp3.name')])}}</template>

        <form-basic :action="['admin.block.import', { course: {{ $course->id }} }]" enctype="multipart/form-data">

            <input-file name="file" label="{{__('t.views.admin.block_import.ecamp3.block_overview')}}" required accept="application/pdf"></input-file>

            <input-hidden name="source" model-value="eCamp3BlockOverview"></input-hidden>

            <button-submit label="{{__('t.views.admin.block_import.import')}}">

                @component('components.help-text', ['key' => 't.views.admin.block_import.ecamp3.how_to_get_the_block_overview', 'id' => 'eCamp3BlockOverviewHelp', 'params' => ['ecamp3' => $ecamp3Link]])
                    <img src="{{ Vite::asset('resources/images/ecamp3_export.png') }}" class="img-fluid w-100 mt-2 border">
                @endcomponent

            </button-submit>

        </form-basic>

    </b-card>

@endsection
