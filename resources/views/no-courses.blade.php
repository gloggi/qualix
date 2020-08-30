@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.welcome.title')}}</template>

        {{ __('t.views.welcome.no_courses') }}

    </b-card>

@endsection
