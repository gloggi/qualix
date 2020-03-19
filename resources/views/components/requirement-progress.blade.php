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
    <div class="progress">
        <div class="progress-bar bg-success" role="progressbar" style="width: {{ 100. * $passed / $total }}%" aria-valuenow="{{ $passed }}" aria-valuemin="0" aria-valuemax="{{ $total }}">
            @if($passed > 0)
                {{trans_choice('t.views.participant_details.qualis.requirements_passed', $passed)}}
            @endif
        </div>
        <div class="progress-bar bg-danger" role="progressbar" style="width: {{ 100. * $failed / $total }}%" aria-valuenow="{{ $failed }}" aria-valuemin="0" aria-valuemax="{{ $total }}">
            @if($failed > 0)
                {{trans_choice('t.views.participant_details.qualis.requirements_failed', $failed)}}
            @endif
        </div>
    </div>
@endif
