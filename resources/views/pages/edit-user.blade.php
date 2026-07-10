@extends('layouts.default')

@section('pagetitle'){{__('t.views.user_settings.page_title') }}@endsection

@section('content')

    <b-card>
        <template #header>{{__('t.views.user_settings.edit')}}</template>

        <form-basic action="user.update" enctype="multipart/form-data">

            <input-text name="name" model-value="{{ $user->name }}" label="{{__('t.models.user.name')}}" required></input-text>

            <input-text name="group" model-value="{{ $user->group }}" label="{{__('t.models.user.group')}}" autofocus></input-text>

            <input-file name="image" label="{{__('t.models.user.image')}}" accept="image/*"></input-file>

            @if($user->image_url)
                <div class="form-group row">
                    <div class="offset-md-3 col-md-6 d-flex align-items-center gap-3">
                        <img src="{{ $user->image_path }}" alt="{{ $user->name }}" class="rounded-circle" width="80" height="80" style="object-fit: cover;">
                        <input-checkbox name="remove_image" label="{{__('t.global.remove')}}" inline></input-checkbox>
                    </div>
                </div>
            @endif

            <button-submit></button-submit>

        </form-basic>

    </b-card>

@endsection
