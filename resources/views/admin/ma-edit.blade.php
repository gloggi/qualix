@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('Mindestanforderung bearbeiten')])

        @component('components.form', ['route' => ['admin.ma.update', ['kurs' => $kurs->id, 'ma' => $ma->id]]])

            @component('components.form.textInput', ['name' => 'anforderung', 'label' => __('Anforderung'), 'required' => true, 'value' => $ma->anforderung])@endcomponent

            @component('components.form.checkboxInput', ['name' => 'killer', 'label' => __('Killer-Kriterium'), 'value' => $ma->killer])@endcomponent

            @component('components.form.submit', ['label' => __('Speichern')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
