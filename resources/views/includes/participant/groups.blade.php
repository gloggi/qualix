<div class="row">

    <div class="col">
        @foreach ($groups as $group)
            <a href="{{ route('observation.new', ['course' => $course->id, 'participant' => $group->participants->implode('id',',')]) }}" class="btn btn-secondary my-1"><i class="fas fa-binoculars"></i> {{__($group->group_name)}}</a>
        @endforeach

    </div>

</div>
