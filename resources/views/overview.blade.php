@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Beobachtungs-Übersicht')])

        @if (count($participants))

            @php
                $columns = [
                    __('TN') => function(\App\Models\Participant $participant) use ($course) { return '<a href="' . route('participants.detail', ['course' => $course->id, 'participant' => $participant->id]) . '">' . (($participant->image_url!=null) ? view('components.img',  ['src' => asset(Storage::url($participant->image_url)), 'classes' => ['avatar-small']]) : '') . $participant->scout_name . '</a>'; },
                    'Total' => function(\App\Models\Participant $participant) { return count($participant->observations->all()); },
                ];
                foreach ($course->users->all() as $user) {
                    $columns[$user->name] = function($participant) use($user) {
                        $count=count(array_filter($participant->observations->all(), function(\App\Models\Observation $observation) use($user) {
                            return $observation->user->id === $user->id;
                        }));
                        return '<div class="responsive-td-background ' . ($count >= 10 ? 'bg-success-light' : ($count < 5 ? 'bg-danger-light' : '')) . '">' . $count . '</div>';
                    };
                }
            @endphp
            @component('components.responsive-table', [
                'data' => $participants,
                'rawColumns' => true,
                'fields' => $columns,
                'cellClass' => 'position-relative',
                'actions' => [
                    'binoculars' => function(\App\Models\Participant $participant) use ($course) { return route('observation.new', ['course' => $course->id, 'participant' => $participant->id]); },
                ]
            ])@endcomponent

        @else

            {{__('Bisher sind keine Teilnehmende erfasst. Bitte erfasse sie')}} <a href="{{ route('admin.participants', ['course' => $course->id]) }}">{{__('hier')}}</a>.

        @endif

    @endcomponent

@endsection
