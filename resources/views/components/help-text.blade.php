@php
    if (!isset($params)) $params = [];
@endphp<span>
    <a href="#" class="text-secondary" data-toggle="collapse" data-target="#{{ $id }}" aria-expanded="true" aria-controls="{{ $id }}">
        {{__($key . '.question', $params)}} <i class="fas fa-question-circle"></i>
    </a>

    <div id="{{ $id }}" class="collapse text-secondary">
        {{__($key . '.answer', $params)}}

        {{ $slot }}
    </div>
</span>
