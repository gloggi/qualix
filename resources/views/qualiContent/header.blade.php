<div class="row mb-3">

    <div class="col-2 mb-3">
        <div class="square-container">
            <img class="card-img-top img img-responsive full-width not-selectable" src="{{ $participant->image_url != null ? asset(Storage::url($participant->image_url)) : asset('images/was-gaffsch.svg') }}" alt="{{ $participant->scout_name }}">
        </div>
    </div>

    <div class="col">
        <div class="d-flex justify-content-between">
            <div>
                <h2>{{__('t.views.quali_content.participant_quali', ['participant' => $participant->scout_name, 'quali' => $quali->name])}}</h2>
                @if (isset($participant->group))<h5>{{ $participant->group }}</h5>@endif
            </div>

            <div>
                <p class="font-weight-bold mb-0">{{ $course->name }}</p>
                @if (isset($course->course_number))<p class="mb-0">{{ $course->course_number }}</p>@endif
                @if($quali->users->count())
                    <div class="my-2">
                        <div>{{__('t.models.quali.users')}}:</div>
                        <div>{{ $quali->users->map->name->join(', ') }}</div>
                    </div>
                @endif
            </div>

        </div>

        {{ $slot }}
    </div>

</div>
