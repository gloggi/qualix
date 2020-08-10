@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.qualis.edit')}}</template>

        @component('components.form', ['route' => ['admin.qualis.update', ['course' => $course->id, 'quali_data' => $quali_data->id]]])

            <input-text name="name" value="{{ $quali_data->name }}" label="{{__('t.models.quali.name')}}" required autofocus></input-text>

            <input-multi-select
                name="participants"
                value="{{ $quali_data->participants->pluck('id')->join(',') }}"
                label="{{__('t.models.quali.participants')}}"
                required
                :options="{{ json_encode($course->participants->map->only('id', 'scout_name')) }}"
                :groups="{{ json_encode([__('t.views.admin.qualis.select_all_participants') => $course->participants->pluck('id')->join(',')]) }}"
                display-field="scout_name"
                multiple></input-multi-select>

            <input-multi-select
                name="requirements"
                value="{{ $quali_data->quali_requirements->map->requirement->pluck('id')->join(',') }}"
                label="{{__('t.models.quali.requirements')}}"
                :options="{{ json_encode($course->requirements->map->only('id', 'content')) }}"
                :groups="{{ json_encode([__('t.views.admin.qualis.select_all_requirements') => $course->requirements->pluck('id')->join(',')]) }}"
                display-field="content"
                multiple></input-multi-select>

            <row-text>
                <b-button variant="link" class="px-0" v-b-toggle.collapse-leader-assignments>
                    {{__('t.views.admin.qualis.leader_assignment')}} <i class="fas fa-caret-down"></i>
                </b-button>
            </row-text>
            <b-collapse id="collapse-leader-assignments" {{ $hideLeaderAssignments ? '' : 'visible' }}>
                @foreach($quali_data->qualis as $quali)
                    <input-multi-select
                        name="qualis[{{ $quali->id }}][user]"
                        value="{{ $quali->user ? $quali->user->id : '' }}"
                        label="{{ $quali->participant->scout_name }}"
                        :options="{{ json_encode($course->users->map->only('id', 'name')) }}"
                        display-field="name"
                        :show-clear="true"></input-multi-select>
                @endforeach
            </b-collapse>

            <button-submit>

                <a href="{{ \Illuminate\Support\Facades\URL::route('admin.qualis', ['course' => $course->id]) }}">{{ __('t.views.admin.qualis.go_back_to_quali_list') }}</a>

            </button-submit>

        @endcomponent

    </b-card>

@endsection
