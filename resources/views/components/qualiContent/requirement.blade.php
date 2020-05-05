<b-card no-body>
    <b-card-header class="pl-3" @if(count($contents))v-b-toggle.collapse-{{ $id }}@endif>
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
    </b-card-header>

    @if(count($contents))
        <b-collapse id="collapse-{{ $id }}">

            @foreach($contents as $content)

                @component('components.qualiContent.' . $content['type'], $content)@endcomponent

            @endforeach

        </b-collapse>
    @endif
</b-card>

