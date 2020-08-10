@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.qualis.edit')}}</template>

        @component('components.form', ['route' => ['admin.qualis.update', ['course' => $course->id, 'quali_data' => $quali_data->id]]])
            <quali-data-form
                v-slot="{ qualiData, allParticipants, formValues }"
                :quali-data="{{ json_encode($quali_data) }}"
                :participants="{{ json_encode($course->participants->map->only('id', 'scout_name')) }}"
                :requirements="{{ json_encode($course->requirements->map->only('id', 'content')) }}"
                :trainers="{{ json_encode($course->users->map->only('id', 'name')) }}"
                :hide-trainer-assignments="{{ $hideTrainerAssignments ? 'true' : 'false' }}"
                back-url="{{ \Illuminate\Support\Facades\URL::route('admin.qualis', ['course' => $course->id]) }}"></quali-data-form>
        @endcomponent

    </b-card>

@endsection
