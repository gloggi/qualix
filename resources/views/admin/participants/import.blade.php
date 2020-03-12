@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('t.views.admin.participants_import.import_from', ['source' => __('t.views.admin.participants_import.MiData.name')])])

        @component('components.form', ['route' => ['admin.participants.import', ['course' => $course->id]], 'enctype' => 'multipart/form-data'])

            @component('components.form.fileInput', ['name' => 'file', 'label' => __('t.views.admin.participants_import.MiData.participant_list'), 'required' => true, 'accept' => 'application/vnd.ms-excel'])@endcomponent

            @component('components.form.hiddenInput', ['name' => 'source', 'value' => 'MiData'])@endcomponent

            @component('components.form.submit', ['label' => __('t.views.admin.participants_import.import')])

                @component('components.help-text', ['key' => 't.views.admin.participants_import.MiData.how_to_get_participant_list', 'id' => 'MiDataParticipantListHelp', 'params' => ['MiData' => $MiDataLink]])

                    <img src="{{ asset('images/MiData-participants-list.png') }}" class="img-fluid w-100 mt-2 border">

                @endcomponent

            @endcomponent

        @endcomponent

    @endcomponent

@endsection
