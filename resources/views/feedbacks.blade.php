@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('t.views.feedbacks.title')}}</template>

        @if($anyResponsibilities)
            <div class="d-flex justify-content-end mb-2">
                <label for="user" class="col-form-label text-md-right mr-2">{{ __('t.views.crib.view_as') }}</label>
                <multi-select
                    name="user"
                    :value="{{ json_encode("$userId") }}"
                    class=""
                    required
                    :options="{{ json_encode($course->users->map->only('id', 'name')->prepend(['id' => 'all', 'name' => __('t.views.feedbacks.show_all')])) }}"
                    display-field="name"
                    @update:selected="selected => $window.location = routeUri('feedbacks', {course: {{ $course->id }}, view: selected.id || 'all'})"></multi-select>
            </div>
        @endif

        @foreach($feedbacks as $feedbackData)
            <b-card no-body>
                <b-card-header v-b-toggle.collapse-{{ $feedbackData->id }}>
                    <h5 class="mb-0">{{ $feedbackData->name }}</h5>
                </b-card-header>

                <b-collapse id="collapse-{{ $feedbackData->id }}" visible>
                    <b-list-group flush>
                        @forelse ($feedbackData->getRelation('feedbacks') as $feedback)
                            <b-list-group-item href="{{ route('feedbackContent.edit', ['course' => $course->id, 'participant' => $feedback->participant->id, 'feedback' => $feedback->id]) }}" class="mb-0 d-flex flex-row align-items-center flex-wrap justify-content-between">
                                <img src="{{ $feedback->participant->image_url != null ? asset(Storage::url($feedback->participant->image_url)) : asset('images/was-gaffsch.svg') }}" class="avatar-small mr-3"/>
                                <h5 class="mb-0 mr-auto">{{ $feedback->participant->scout_name }}</h5>
                                <requirement-progress v-if="{{ json_encode($feedback->requirements->count()) }}" class="w-100 w-md-50 mt-2 justify-self-end" :requirements="{{ json_encode($feedback->requirements) }}"></requirement-progress>
                            </b-list-group-item>
                        @empty
                            <b-list-group-item class="mb-0">
                                {{ __('t.views.feedbacks.none_assigned_to_user', ['user' => $user?->name]) }} <a href="{{ route('admin.feedbacks.edit', ['course' => $course->id, 'feedback_data' => $feedbackData->id, 'highlight' => 'assignments']) }}">{{ __('t.views.feedbacks.edit_responsibles') }}</a>
                            </b-list-group-item>
                        @endforelse
                    </b-list-group>
                </b-collapse>
            </b-card>
        @endforeach

    </b-card>

@endsection
