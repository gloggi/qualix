<div class="card">
    @if(isset($header))
        <div class="card-header">{{ $header }}</div>
    @endif

    <div class="card-body">
        {{ $slot }}
    </div>
</div>
