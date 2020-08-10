@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.participant_import.import_from', ['source' => __('t.views.admin.participant_import.MiData.name')])}}</template>

        @component('components.form', ['route' => ['admin.participants.import', ['course' => $course->id]], 'enctype' => 'multipart/form-data'])

            <input-file name="file" label="{{__('t.views.admin.participant_import.MiData.participant_list')}}" required accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,text/csv"></input-file>

            <input-hidden name="source" value="MiDataParticipantList"></input-hidden>

            <button-submit label="{{__('t.views.admin.participant_import.import')}}">

                @component('components.help-text', ['key' => 't.views.admin.participant_import.MiData.how_to_get_the_participant_list', 'id' => 'MiDataParticipantListHelp', 'params' => ['MiData' => $MiDataLink]])

                    <img src="{{ asset('images/MiData-participant-list.png') }}" class="img-fluid w-100 mt-2 border">

                @endcomponent

            </button-submit>

        @endcomponent

    </b-card>

@endsection
