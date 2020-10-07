@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.qualis.edit')}}</template>

        <form-quali-data
            :action="['admin.qualis.update', { course: {{ $course->id }}, quali_data: {{ $quali_data->id }} }]"
            name="{{ $quali_data->name }}"
            :qualis="{{ json_encode($quali_data->qualis) }}"
            :participants="{{ json_encode($course->participants->map->only('id', 'scout_name')) }}"
            :requirements="{{ json_encode($course->requirements->map->only('id', 'content')) }}"
            :trainers="{{ json_encode($course->users->map->only('id', 'name')) }}">

            <template #submit>
                <button-submit>
                    <a href="{{ \Illuminate\Support\Facades\URL::route('admin.qualis', ['course' => $course->id]) }}">{{__('t.views.admin.qualis.go_back_to_quali_list')}}</a>
                </button-submit>
            </template>

        </form-quali-data>

    </b-card>

@endsection
