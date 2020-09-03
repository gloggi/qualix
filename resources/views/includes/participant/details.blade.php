<b-card body-class="container-fluid">
    <template #header>{{__('t.views.participant_details.title')}}</template>

    <div class="row my-3">

        <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
            <div class="square-container">
                <img class="card-img-top img img-responsive full-width" src="{{ $participant->image_url != null ? asset(Storage::url($participant->image_url)) : asset('images/was-gaffsch.svg') }}" alt="{{ $participant->scout_name }}">
            </div>
        </div>

        <div class="col">
            <h3>{{ $participant->scout_name }}</h3>
            @if (isset($participant->group))<h5>{{ $participant->group }}</h5>@endif
            <p>{{ trans_choice('t.views.participant_details.num_observations', $participant->observations, ['positive' => $participant->positive->count(), 'neutral' => $participant->neutral->count(), 'negative' => $participant->negative->count()])}}</p>
            @php
                $columns = [];
                foreach ($course->users->all() as $user) {
                    $columns[$user->name] = function($observations) use($user) { return count(array_filter($observations, function(\App\Models\Observation $observation) use($user) {
                        return $observation->user->id === $user->id;
                    })); };
                }
            @endphp
            @component('components.responsive-table', [
                'data' => [$participant->observations->all()],
                'fields' => $columns,
            ])@endcomponent
            <a href="{{ route('observation.new', ['course' => $course->id, 'participant' => $participant->id]) }}" class="btn btn-primary"><i class="fas fa-binoculars"></i> {{__('t.global.add_observation')}}</a>
        </div>

    </div>
    @component('includes.participant.groups', ['groups' => $participant->participant_groups])@endcomponent


</b-card>
