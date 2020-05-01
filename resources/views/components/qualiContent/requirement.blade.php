<div class="card">
    <div class="card-header pl-3" id="heading{{ $id }}" @if(count($contents))data-toggle="collapse" data-target="#collapse{{ $id }}" aria-expanded="true" aria-controls="collapse{{ $id }}"@endif>
        <h5 class="mb-0 d-flex justify-content-between">
            <span>
                @if($passed === 1)
                    <i class="text-success fas fa-check-circle mr-1"></i>
                @elseif($passed === 0)
                    <i class="text-danger fas fa-times-circle mr-1"></i>
                @else
                    <i class="text-secondary fas fa-binoculars mr-1"></i>
                @endif

                {{ $content }}
            </span>
            @if(count($contents))
                <i class="fas fa-caret-down"></i>
            @endif
        </h5>
    </div>

    @if(count($contents))
        <div id="collapse{{ $id }}" class="card-body @if(count($contents))collapse @endif show px-4" aria-labelledby="heading{{ $id }}">

            @foreach($contents as $content)

                @component('components.qualiContent.' . $content['type'], $content)@endcomponent

            @endforeach

        </div>
    @endif
</div>

