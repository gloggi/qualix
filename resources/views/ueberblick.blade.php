@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Beobachtungs-Übersicht')])

        @php
            $columns = [
                __('TN') => function(\App\Models\TN $tn) use ($kurs) { return '<a href="' . route('tn.detail', ['kurs' => $kurs->id, 'tn' => $tn->id]) . '">' . $tn->pfadiname . '</a>'; },
                'Total' => function(\App\Models\TN $tn) { return count($tn->beobachtungen->all()); },
            ];
            foreach ($kurs->users->all() as $user) {
                $columns[$user->name] = function($tn) use($user) {
                    $count=count(array_filter($tn->beobachtungen->all(), function(\App\Models\Beobachtung $beobachtung) use($user) {
                        return $beobachtung->user->id === $user->id;
                    }));
                    return '<div class="td-background ' . ($count >= 10 ? 'bg-success text-white' : ($count < 5 ? 'bg-danger text-white' : '')) . '">' . $count . '</div>'; };
            }
        @endphp
        @component('components.responsive-table', [
            'data' => $tns,
            'rawColumns' => true,
            'fields' => $columns,
            'actions' => [
                'binoculars' => function(\App\Models\TN $tn) use ($kurs) { return route('beobachtung.neu', ['kurs' => $kurs->id, 'tn' => $tn->id]); },
            ]
        ])@endcomponent

    @endcomponent

@endsection
