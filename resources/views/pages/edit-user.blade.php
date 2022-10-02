@extends('layouts.default')

@section('pagetitle'){{__('t.views.user_settings.page_title') }}@endsection

@section('content')

    <b-card>
        <template #header>{{__('t.views.user_settings.edit')}}</template>

        <form-basic action="user.update" enctype="multipart/form-data">

            <input-text name="name" value="{{ $user->name }}" label="{{__('t.models.user.name')}}" required></input-text>

            <input-text name="group" value="{{ $user->group }}" label="{{__('t.models.user.group')}}" autofocus></input-text>

            <input-file name="image" label="{{__('t.models.user.image')}}" accept="image/*"></input-file>

            <button-submit></button-submit>

        </form-basic>

    </b-card>

@endsection
