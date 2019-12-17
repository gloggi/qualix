@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('t.views.user_settings.edit')])

        @component('components.form', ['route' => ['user.update'], 'enctype' => 'multipart/form-data'])

            @component('components.form.textInput', ['name' => 'name', 'label' => __('t.models.user.name'), 'required' => true, 'value' => $user->name])@endcomponent

            @component('components.form.textInput', ['name' => 'group', 'label' => __('t.models.user.group'), 'autofocus' => true, 'value' => $user->group])@endcomponent

            @component('components.form.fileInput', ['name' => 'image', 'label' => __('t.models.user.image'), 'accept' => 'image/*'])@endcomponent

            @component('components.form.submit', ['label' => __('t.global.save')])@endcomponent

        @endcomponent

    @endcomponent

@endsection
