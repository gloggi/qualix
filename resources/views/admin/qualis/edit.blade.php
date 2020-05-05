@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.qualis.edit')}}</template>

        @component('components.form', ['route' => ['admin.qualis.update', ['course' => $course->id, 'quali' => $quali_data->id]]])

            @component('components.form.textInput', ['name' => 'name', 'label' => __('t.models.quali.name'), 'value' => $quali_data->name, 'required' => true, 'autofocus' => true])@endcomponent

            @component('components.form.multiSelectInput', [
                'name' => 'participants',
                'label' => __('t.models.quali.participants'),
                'required' => true,
                'value' => $quali_data->participants->all(),
                'options' => $course->participants->all(),
                'groups' => [__('t.views.admin.qualis.select_all_participants') => $course->participants->all()],
                'valueFn' => function(\App\Models\Participant $participant) { return $participant->id; },
                'displayFn' => function(\App\Models\Participant $participant) { return $participant->scout_name; },
                'multiple' => true
            ])@endcomponent

            @component('components.form.multiSelectInput', [
                'name' => 'requirements',
                'label' => __('t.models.quali.requirements'),
                'required' => false,
                'value' => $quali_data->requirements->all(),
                'options' => $course->requirements->all(),
                'groups' => [__('t.views.admin.qualis.select_all_requirements') => $course->requirements->all()],
                'valueFn' => function(\App\Models\Requirement $requirement) { return $requirement->id; },
                'displayFn' => function(\App\Models\Requirement $requirement) { return $requirement->content; },
                'multiple' => true
            ])@endcomponent

            @component('components.form.text')
                <b-button variant="link" class="px-0" v-b-toggle.collapse-leader-assignments>
                    {{__('t.views.admin.qualis.leader_assignment')}} <i class="fas fa-caret-down"></i>
                </b-button>
            @endcomponent
            <b-collapse id="collapse-leader-assignments" {{ $hideLeaderAssignments ? '' : 'visible' }}>
                @foreach($quali_data->qualis as $quali)
                    @component('components.form.multiSelectInput', [
                        'name' => 'qualis[' . $quali->id . '][user]',
                        'label' => $quali->participant->scout_name,
                        'required' => false,
                        'value' => $quali->user,
                        'options' => $course->users->all(),
                        'valueFn' => function(\App\Models\User $user) { return $user->id; },
                        'displayFn' => function(\App\Models\User $user) { return $user->name; },
                        'multiple' => false,
                        'showClear' => true,
                    ])@endcomponent
                @endforeach
            </b-collapse>

            @component('components.form.submit', ['label' => __('t.global.save')])

                <a href="{{ \Illuminate\Support\Facades\URL::route('admin.qualis', ['course' => $course->id]) }}">{{ __('t.views.admin.qualis.go_back_to_quali_list') }}</a>

            @endcomponent

        @endcomponent

    </b-card>

@endsection
