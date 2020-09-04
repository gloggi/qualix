@component('components.responsive-table', [
    'data' => $observations,
    'fields' => [
        __('t.models.observation.content') => function(\App\Models\Observation $observation) use($course) {
            $rendered = new App\Util\HtmlString;
            if ($observation->participants()->count() > 1) {
                $rendered->s('<div>');
                $observation->participants()->each(function(\App\Models\Participant $participant) use($rendered, $course) {
                    $rendered->s('<a href="' . route('participants.detail', ['course' => $course->id, 'participant' => $participant->id]) . '" class="badge badge-primary mr-1">')->e($participant->scout_name)->s('</a>');
                });
                $rendered->s('</div>');
            }
            return $rendered->e($observation->content);
        },
        __('t.models.observation.block') => function(\App\Models\Observation $observation) { return $observation->block->blockname_and_number; },
        __('t.models.observation.requirements') => function(\App\Models\Observation $observation) {
            return (new App\Util\HtmlString)->s(implode('', array_map(function(\App\Models\Requirement $requirement) {
                return (new App\Util\HtmlString)->s('<span class="white-space-normal badge badge-' . ($requirement->mandatory ? 'warning' : 'info') . '">')->e($requirement->content)->s('</span>');
            }, $observation->requirements->all())));
        },
        __('t.models.observation.impression') => function(\App\Models\Observation $observation) {
            $impmression = $observation->impression;
            if ($impmression === 0) return (new App\Util\HtmlString)->s('<span class="badge badge-danger">')->__('t.global.negative')->s('</span>');
            else if ($impmression === 2) return (new App\Util\HtmlString)->s('<span class="badge badge-success">')->__('t.global.positive')->s('</span>');
            else return (new App\Util\HtmlString)->s('<span class="badge badge-secondary">')->__('t.global.neutral')->s('</span>');
        },
        __('t.models.observation.user') => function(\App\Models\Observation $observation) { return $observation->user->name; }
    ],
    'actions' => [
        'edit' => function(\App\Models\Observation $observation) use ($course) { return route('observation.edit', ['course' => $course->id, 'observation' => $observation->id]); },
        'delete' => function(\App\Models\Observation $observation) use ($course) { return [
            'text' => __('t.views.participant_details.really_delete_observation'),
            'route' => ['observation.delete', ['course' => $course->id, 'observation' => $observation->id]],
         ];},
    ]
])@endcomponent
