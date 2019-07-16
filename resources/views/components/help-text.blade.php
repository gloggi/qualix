<span>
    <a href="#" data-toggle="collapse" data-target="#{{ $collapseId }}" aria-expanded="true" aria-controls="{{ $collapseId }}">
        {{ $header }} <i class="fas fa-question-circle"></i>
    </a>

    <div id="{{ $collapseId }}" class="collapse">
        {{ $slot }}
    </div>
</span>
