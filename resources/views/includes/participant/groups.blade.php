<div class="row my-3">

    <div class="col">
        @foreach ($groups as $group)
            <a href="{{ route('observation.new', ['course' => $course->id, 'group' => $group->id]) }}" class="btn btn-secondary"><i class="fas fa-binoculars"></i> {{__($group->group_name)}}</a>
        @endforeach

    </div>

</div>
