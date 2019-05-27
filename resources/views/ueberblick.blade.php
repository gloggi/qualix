@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Beobachtungs-Übersicht')])

        @if (count($kurs->tns))

            @php
                $columns = [
                    __('TN') => function(\App\Models\TN $tn) use ($kurs) { return '<a href="' . route('tn.detail', ['kurs' => $kurs->id, 'tn' => $tn->id]) . '">' . (($tn->bild_url!=null) ? view('components.img',  ['src' => asset(Storage::url($tn->bild_url)), 'classes' => ['avatar-small']]) : '') . $tn->pfadiname . '</a>'; },
                    'Total' => function(\App\Models\TN $tn) { return count($tn->beobachtungen->all()); },
                ];
                foreach ($kurs->users->all() as $user) {
                    $columns[$user->name] = function($tn) use($user) {
                        $count=count(array_filter($tn->beobachtungen->all(), function(\App\Models\Beobachtung $beobachtung) use($user) {
                            return $beobachtung->user->id === $user->id;
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
                    'binoculars' => function(\App\Models\TN $tn) use ($kurs) { return route('beobachtung.neu', ['kurs' => $kurs->id, 'tn' => $tn->id]); },
                ]
            ])@endcomponent

        @else

            {{__('Bisher sind keine Teilnehmende erfasst. Bitte erfasse sie')}} <a href="{{ route('admin.tn', ['kurs' => $kurs->id]) }}">{{__('hier')}}</a>.

        @endif

    @endcomponent

@endsection
