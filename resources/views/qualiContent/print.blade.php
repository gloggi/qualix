@extends('layouts.print')

@section('pagetitle'){{ $quali->name }} {{ $participant->scout_name }}@endsection

@section('content')

    @component('qualiContent.header', ['quali' => $quali, 'participant' => $participant, 'course' => $course])
        @if($quali->requirements()->count())
            <div class="mt-2">
                <h5>{{__('t.views.quali_content.requirements_status')}}</h5>
                <requirement-progress :requirements="{{ json_encode($quali->requirements) }}"></requirement-progress>
            </div>
        @endif
    @endcomponent

    <quali-editor
        readonly
        name=""
        :value="{{ json_encode($quali->contents) }}"
        :observations="{{ json_encode($observations) }}"
        :requirements="{{ json_encode($quali->requirements) }}"
        @content-ready="() => $window.PagedPolyfill.preview()"></quali-editor>

@endsection
