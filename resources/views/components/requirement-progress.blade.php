@php
$requirements = $quali->requirements()->get();
$passed = $requirements->filter(function ($requirement) {
    return $requirement->passed === 1;
})->count();
$failed = $requirements->filter(function ($requirement) {
    return $requirement->passed === 0;
})->count();
$total = $requirements->count();

@endphp
@if($total > 0)
    <b-progress>
        <b-progress-bar variant="success" value="{{ $passed }}" max="{{ $total }}">
            @if($passed > 0)
                {{trans_choice('t.views.participant_details.qualis.requirements_met', $passed)}}
            @endif
        </b-progress-bar>
        <b-progress-bar variant="danger" value="{{ $failed }}" max="{{ $total }}">
            @if($failed > 0)
                {{trans_choice('t.views.participant_details.qualis.requirements_failed', $failed)}}
            @endif
        </b-progress-bar>
    </b-progress>
@endif
