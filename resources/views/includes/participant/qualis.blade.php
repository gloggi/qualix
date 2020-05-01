<b-card>
    <template #header>{{__('t.views.participant_details.qualis.title')}}</template>

    @php
    $fields = [
        __('t.models.quali.name') => function(\App\Models\Quali $quali) use($course, $participant) {
            return (new App\Util\HtmlString)->s('<a href="' . route('qualiContent.detail', ['course' => $course->id, 'participant' => $participant->id, 'quali' => $quali->id]) . '">')->e($quali->name)->s('</a>');
        },
    ];
    if ($participant->qualis()->whereHas('requirements')->exists()) {
        $fields[__('t.models.quali.requirement_progress')] = function(\App\Models\Quali $quali) {
            return (new App\Util\HtmlString)->s(view('components.requirement-progress', ['quali' => $quali])->render());
        };
    }
    if ($participant->qualis()->whereNotNull('user_id')->exists()) {
        $fields[__('t.models.quali.user')] = function(\App\Models\Quali $quali) { return $quali->user ? $quali->user->name : ''; };
    }
    @endphp
    @component('components.responsive-table', [
        'data' => $participant->qualis,
        'fields' => $fields,
        'actions' => [
            'edit' => function(\App\Models\Quali $quali) use ($course, $participant) { return route('qualiContent.detail', ['course' => $course->id, 'participant' => $participant->id, 'quali' => $quali->id]); },
        ]
    ])@endcomponent

</b-card>
