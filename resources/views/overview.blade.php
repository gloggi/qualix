@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Beobachtungs-Übersicht')])

        @if (count($course->participants))

            @php
                $columns = [
                    __('TN') => function(\App\Models\Participant $tn) use ($course) { return '<a href="' . route('tn.detail', ['course' => $course->id, 'tn' => $tn->id]) . '">' . (($tn->image_url!=null) ? view('components.img',  ['src' => asset(Storage::url($tn->image_url)), 'classes' => ['avatar-small']]) : '') . $tn->scout_name . '</a>'; },
                    'Total' => function(\App\Models\Participant $tn) { return count($tn->observations->all()); },
                ];
                foreach ($course->users->all() as $user) {
                    $columns[$user->name] = function($tn) use($user) {
                        $count=count(array_filter($tn->observations->all(), function(\App\Models\Observation $observation) use($user) {
                            return $observation->user->id === $user->id;
                        }));
                        return '<div class="responsive-td-background ' . ($count >= 10 ? 'bg-success-light' : ($count < 5 ? 'bg-danger-light' : '')) . '">' . $count . '</div>'; };
                }
            @endphp
            @component('components.responsive-table', [
                'data' => $tns,
                'rawColumns' => true,
                'fields' => $columns,
                'cellClass' => 'position-relative',
                'actions' => [
                    'binoculars' => function(\App\Models\Participant $tn) use ($course) { return route('observation.new', ['course' => $course->id, 'tn' => $tn->id]); },
                ]
            ])@endcomponent

        @else

            {{__('Bisher sind keine Teilnehmende erfasst. Bitte erfasse sie')}} <a href="{{ route('admin.participants', ['course' => $course->id]) }}">{{__('hier')}}</a>.

        @endif

    @endcomponent

@endsection
