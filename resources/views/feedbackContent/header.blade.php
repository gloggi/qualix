<div class="row mb-3">

    <div class="col-2 mb-3">
        <div class="square-container">
            <img class="card-img-top img img-responsive full-width not-selectable" src="{{ $participant->image_url != null ? asset(Storage::url($participant->image_url)) : '/was-gaffsch.svg' }}" alt="{{ $participant->scout_name }}">
        </div>
    </div>

    <div class="col">
        <div class="d-flex justify-content-between">
            <div>
                <h2>{{__('t.views.feedback_content.participant_feedback', ['participant' => $participant->scout_name, 'feedback' => $feedback->name])}}</h2>
                @if (isset($participant->group))<h5>{{ $participant->group }}</h5>@endif
            </div>

            <div>
                <p class="font-weight-bold mb-0">{{ $course->name }}</p>
                @if (isset($course->course_number))<p class="mb-0">{{ $course->course_number }}</p>@endif
                @if($feedback->users->count())
                    <div class="my-2">
                        <div>{{__('t.models.feedback.users')}}:</div>
                        <div>{{ $feedback->users->map->name->join(', ') }}</div>
                    </div>
                @endif
            </div>

        </div>

        {{ $slot }}
    </div>

</div>
