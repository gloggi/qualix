@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('TN Details'), 'bodyClass' => 'container-fluid'])

        <div class="row my-3">

            <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                <div class="square-container">
                    <img class="card-img-top img img-responsive full-width" src="{{ $tn->bild_url != null ? asset(Storage::url($tn->bild_url)) : asset('images/was-gaffsch.svg') }}" alt="{{ $tn->pfadiname }}">
                </div>
            </div>

            <div class="col">
                <h3>{{ $tn->pfadiname }}</h3>
                @if (isset($tn->abteilung))<h5>{{ $tn->abteilung }}</h5>@endif
                <p>{{ trans_choice('{0}Keine Beobachtungen|{1}1 Beobachtung|[2,*]:count Beobachtungen', count($tn->beobachtungen), ['count' => count($tn->beobachtungen)])}}, {{ __('davon :positive positive, :neutral neutrale und :negative negative Beobachtungen.', ['positive' => $tn->positive->count(), 'neutral' => $tn->neutral->count(), 'negative' => $tn->negative->count()])}}</p>
                </ul>
                @php
                    $columns = [];
                    foreach ($kurs->users->all() as $user) {
                        $columns[$user->name] = function($beobachtungen) use($user) { return count(array_filter($beobachtungen, function(\App\Models\Beobachtung $beobachtung) use($user) {
                            return $beobachtung->user->id === $user->id;
                        })); };
                    }
                @endphp
                @component('components.responsive-table', [
                    'data' => [$tn->beobachtungen->all()],
                    'fields' => $columns,
                ])@endcomponent
                <a href="{{ route('beobachtung.neu', ['kurs' => $kurs->id, 'tn' => $tn->id]) }}" class="btn btn-primary"><i class="fas fa-binoculars"></i> {{__('Beobachtung erfassen')}}</a>
            </div>

        </div>

    @endcomponent

    @component('components.card', ['header' => __('Beobachtungen')])

        @component('components.responsive-table', [
            'data' => $tn->beobachtungen,
            'rawColumns' => true,
            'fields' => [
                __('Beobachtung') => function(\App\Models\Beobachtung $beobachtung) { return $beobachtung->kommentar; },
                __('Block') => function(\App\Models\Beobachtung $beobachtung) { return $beobachtung->block->blockname; },
                __('MA') => function(\App\Models\Beobachtung $beobachtung) {
                    return implode('', array_map(function(\App\Models\MA $ma) {
                        return '<span class="badge badge-' . ($ma->killer ? 'warning' : 'info') . '" style="white-space: normal">' . $ma->anforderung . '</span>';
                    }, $beobachtung->mas->all()));
                },
                __('Bewertung') => function(\App\Models\Beobachtung $beobachtung) {
                    $bewertung = $beobachtung->bewertung;
                    if ($bewertung === 0) return '<span class="badge badge-danger">negativ</span>';
                    else if ($bewertung === 2) return '<span class="badge badge-success">positiv</span>';
                    else return '<span class="badge badge-secondary">neutral</span>';
                },
                __('Beobachter') => function(\App\Models\Beobachtung $beobachtung) { return $beobachtung->user->name; }
            ],
            'actions' => [
                'edit' => function(\App\Models\Beobachtung $beobachtung) use ($kurs) { return route('beobachtung.edit', ['kurs' => $kurs->id, 'beobachtung' => $beobachtung->id]); },
                'delete' => function(\App\Models\Beobachtung $beobachtung) use ($kurs) { return [
                    'text' => __('Willst du diese Beobachtung wirklich löschen?'),
                    'route' => ['beobachtung.delete', ['kurs' => $kurs->id, 'beobachtung' => $beobachtung->id]],
                 ];},
            ]
        ])@endcomponent

    @endcomponent

@endsection
