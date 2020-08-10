@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.qualis.new')}}</template>

        @component('components.form', ['route' => ['admin.qualis.store', ['course' => $course->id]]])
            <quali-data-form
                :participants="{{ json_encode($course->participants->map->only('id', 'scout_name')) }}"
                :requirements="{{ json_encode($course->requirements->map->only('id', 'content')) }}"
                :trainers="{{ json_encode($course->users->map->only('id', 'name')) }}"
                quali-notes-template
                back-url="{{ \Illuminate\Support\Facades\URL::route('admin.qualis', ['course' => $course->id]) }}">

                <input-textarea name="quali_notes_template" label="{{__('t.views.admin.qualis.quali_notes_template')}}">

                    @component('components.help-text', ['id' => 'qualiNotesTemplateHelp', 'key' => 't.views.admin.qualis.quali_notes_template_description'])@endcomponent

                </input-textarea>

                <template #submit>
                    <button-submit label="{{__('t.views.admin.qualis.create')}}">
                        @component('components.help-text', ['id' => 'qualiHelp', 'key' => 't.views.admin.qualis.what_are_qualis'])@endcomponent
                    </button-submit>
                </template>

            </quali-data-form>

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
                        'route' => ['admin.qualis.delete', ['course' => $course->id, 'quali_data' => $qualiData->id]],
                     ]; },
                ]
            ])@endcomponent

        @else

            {{__('t.views.admin.qualis.no_qualis')}}

        @endif

    </b-card>

@endsection
