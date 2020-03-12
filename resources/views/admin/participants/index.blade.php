@extends('layouts.default')

@section('content')

    @component('components.card', ['header' => __('t.views.admin.participants.new')])

        @component('components.form', ['route' => ['admin.participants.store', ['course' => $course->id]], 'enctype' => 'multipart/form-data'])

            @component('components.form.textInput', ['name' => 'scout_name', 'label' => __('t.models.participant.scout_name'), 'required' => true, 'autofocus' => true])@endcomponent

            @component('components.form.textInput', ['name' => 'group', 'label' => __('t.models.participant.group')])@endcomponent

            @component('components.form.fileInput', ['name' => 'image', 'label' => __('t.models.participant.image'), 'accept' => 'image/*'])@endcomponent

            @component('components.form.submit', ['label' => __('t.global.add')])@endcomponent
            <a class="btn btn-link mb-1" href="{{ route('admin.participants.import', ['course' => $course]) }}">
                {{ __('t.views.admin.participants.import') }}
            </a>

        @endcomponent

    @endcomponent

    @component('components.card', ['header' => __('t.views.admin.participants.existing', ['courseName' => $course->name])])

        @if (count($course->participants))

            @component('components.responsive-table', [
                'data' => $course->participants,
                'image' => [
                    __('t.models.participant.image') => function(\App\Models\Participant $participant) { return ($participant->image_url!=null) ? (new App\Util\HtmlString)->s(view('components.img',  ['src' => asset(Storage::url($participant->image_url)), 'classes' => ['avatar-small']])) : ''; },
                ],
                'fields' => [
                    __('t.models.participant.scout_name') => function(\App\Models\Participant $participant) { return $participant->scout_name; },
                    __('t.models.participant.group') => function(\App\Models\Participant $participant) { return $participant->group; },
                ],
                'actions' => [
                    'edit' => function(\App\Models\Participant $participant) use ($course) { return route('admin.participants.edit', ['course' => $course->id, 'participant' => $participant->id]); },
                    'delete' => function(\App\Models\Participant $participant) use ($course) { return [
                        'text' => __('t.views.admin.participants.really_remove', ['name' => $participant->scout_name]) . ($course->archived ? '' : ' ' . trans_choice('t.views.admin.participants.observations_on_participant', $participant->observations)),
                        'route' => ['admin.participants.delete', ['course' => $course->id, 'participant' => $participant->id]],
                     ];},
                ]
            ])@endcomponent

        @else

            {{__('t.views.admin.participants.no_participants')}}

        @endif

    @endcomponent

@endsection
