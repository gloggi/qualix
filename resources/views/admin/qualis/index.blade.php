@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.qualis.new')}}</template>

        @component('components.form', ['route' => ['admin.qualis.store', ['course' => $course->id]]])

            @component('components.form.textInput', ['name' => 'name', 'label' => __('t.models.quali.name'), 'required' => true, 'autofocus' => true])@endcomponent

            @component('components.form.multiSelectInput', [
                'name' => 'participants',
                'label' => __('t.models.quali.participants'),
                'required' => true,
                'value' => $course->participants->all(),
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
                'value' => $course->requirements->all(),
                'options' => $course->requirements->all(),
                'groups' => [__('t.views.admin.qualis.select_all_requirements') => $course->requirements->all()],
                'valueFn' => function(\App\Models\Requirement $requirement) { return $requirement->id; },
                'displayFn' => function(\App\Models\Requirement $requirement) { return $requirement->content; },
                'multiple' => true
            ])@endcomponent

            @component('components.form.textareaInput', ['name' => 'quali_notes_template', 'label' => __('t.views.admin.qualis.quali_notes_template')])

                @component('components.help-text', ['id' => 'qualiNotesTemplateHelp', 'key' => 't.views.admin.qualis.quali_notes_template_description'])@endcomponent

            @endcomponent

            @component('components.form.submit', ['label' => __('t.views.admin.qualis.create')])

                @component('components.help-text', ['id' => 'qualiHelp', 'key' => 't.views.admin.qualis.what_are_qualis'])@endcomponent

            @endcomponent

        @endcomponent

    </b-card>

    <b-card>
        <template #header>{{__('t.views.admin.qualis.existing', ['courseName' => $course->name])}}</template>

        @if (count($course->quali_datas))

            @php
                $fields = [
                    __('t.models.quali.name') => function(\App\Models\QualiData $qualiData) { return $qualiData->name; },
                ];
            @endphp
            @component('components.responsive-table', [
                'data' => $course->quali_datas,
                'fields' => $fields,
                'actions' => [
                    'edit' => function(\App\Models\QualiData $qualiData) use ($course) { return route('admin.qualis.edit', ['course' => $course->id, 'quali_data' => $qualiData->id]); },
                    'delete' => function(\App\Models\QualiData $qualiData) use ($course) { return [
                        'text' => __('t.views.admin.qualis.really_delete', ['name' => $qualiData->name]),
                        'route' => ['admin.qualis.delete', ['course' => $course->id, 'quali' => $qualiData->id]],
                     ]; },
                ]
            ])@endcomponent

        @else

            {{__('t.views.admin.qualis.no_qualis')}}

        @endif

    </b-card>

@endsection
