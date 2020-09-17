<div class="d-flex align-items-end w-100 mb-2">
    <div><a class="btn-link" href="#">Alle Anforderungen einklappen</a></div>
</div>

<div>
    @foreach($quali->contents as $content)

        @component('components.qualiContent.' . $content['type'], $content)@endcomponent

    @endforeach
</div>
