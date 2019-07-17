<span>
    <a href="#" class="text-secondary" data-toggle="collapse" data-target="#{{ $collapseId }}" aria-expanded="true" aria-controls="{{ $collapseId }}">
        {{ $header }} <i class="fas fa-question-circle"></i>
    </a>

    <div id="{{ $collapseId }}" class="collapse text-secondary">
        {{ $slot }}
    </div>
</span>
