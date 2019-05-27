<div class="card">
    @if(isset($header))
        <div class="card-header">{{ $header }}</div>
    @endif

    <div class="{{ isset($bodyClass) ? $bodyClass : 'card-body' }}">
        {{ $slot }}
    </div>
</div>
