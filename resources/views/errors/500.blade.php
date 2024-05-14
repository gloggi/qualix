@extends('layouts.default')

@section('pagetitle'){{__('t.views.error_page.page_title') }}@endsection

@section('content')

    <b-card>
        <template #header>{{__('Server Error')}}</template>

        <p>{{__('t.views.error_page.please_try_again_later')}}</p>

        <a class="btn btn-link mb-1" href="{{ url()->previous() }}">
            {{ __('t.views.error_page.back') }}
        </a>

    </b-card>

@endsection
