@component('components.form', ['route' => ['qualiContent.update', ['course' => $course->id, 'participant' => $participant->id, 'quali' => $quali->id]]])
    <form-quali-content
        :quali="{{ json_encode($quali) }}"
        :observations="{{ json_encode($observations) }}"
        :requirements="{{ json_encode($quali->requirements) }}"></form-quali-content>
@endcomponent
