<div class="card {{ $class ?? '' }}" {{ $attrs ?? '' }}>
    @if(isset($header) || isset($headerRight))
        <div class="card-header d-flex justify-content-between">
            <span>{{ $header ?? '' }}</span>
            <span class="text-right">{{ $headerRight ?? '' }}</span>
        </div>
    @endif

    <div class="{{ $bodyClass ?? 'card-body' }}">
        {{ $slot }}
    </div>
</div>
