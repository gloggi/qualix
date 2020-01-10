@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('t.views.admin.block_import.import_from', ['source' => __('t.views.admin.block_import.ecamp2.name')])])

        @component('components.form', ['route' => ['admin.block.import', ['course' => $course->id]], 'enctype' => 'multipart/form-data'])

            @component('components.form.fileInput', ['name' => 'file', 'label' => __('t.views.admin.block_import.ecamp2.block_overview'), 'required' => true, 'accept' => 'application/vnd.ms-excel'])@endcomponent

            @component('components.form.hiddenInput', ['name' => 'source', 'value' => 'eCamp2BlockOverview'])@endcomponent

            @component('components.form.submit', ['label' => __('t.views.admin.block_import.import')])

                @component('components.help-text', ['key' => 't.views.admin.block_import.ecamp2.how_to_get_the_block_overview', 'id' => 'eCamp2BlockOverviewHelp', 'params' => ['ecamp2' => $ecamp2Link]])

                    <img src="{{ asset('images/ecamp2-block-overview.png') }}" class="img-fluid w-100 mt-2 border">

                @endcomponent

            @endcomponent

        @endcomponent

    @endcomponent

@endsection
