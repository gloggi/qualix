@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('t.views.admin.participant_import.import_from', ['source' => __('t.views.admin.participant_import.MiData.name')])])

        @component('components.form', ['route' => ['admin.participants.import', ['course' => $course->id]], 'enctype' => 'multipart/form-data'])

            @component('components.form.fileInput', ['name' => 'file', 'label' => __('t.views.admin.participant_import.MiData.participant_list'), 'required' => true, 'accept' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,text/csv'])@endcomponent

            @component('components.form.hiddenInput', ['name' => 'source', 'value' => 'MiDataParticipantList'])@endcomponent

            @component('components.form.submit', ['label' => __('t.views.admin.participant_import.import')])

                @component('components.help-text', ['key' => 't.views.admin.participant_import.MiData.how_to_get_the_participant_list', 'id' => 'MiDataParticipantListHelp', 'params' => ['MiData' => $MiDataLink]])

                    <img src="{{ asset('images/MiData-participant-list.png') }}" class="img-fluid w-100 mt-2 border">

                @endcomponent

            @endcomponent

        @endcomponent

    @endcomponent

@endsection
