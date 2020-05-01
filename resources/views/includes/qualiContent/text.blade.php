<div class="d-flex justify-content-between mb-2">
    <a class="btn-link" href="{{ route('qualiContent.edit', ['course' => $course->id, 'participant' => $participant->id, 'quali' => $quali->id]) }}">{{__('t.global.edit')}} <i class="fas fa-edit"></i></a>
    <div><a class="btn-link" href="#">Alle Anforderungen einklappen</a></div>
</div>

<div>
    @foreach($quali->contents as $content)

        @component('components.qualiContent.' . $content['type'], $content)@endcomponent

    @endforeach
</div>
