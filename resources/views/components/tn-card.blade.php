<div class="col">
    <a href="{{ $link }}">
        <div class="card mb-4">
            @if ($image != null)<img class="card-img-top" src="{{ asset(Storage::url($image)) }}" alt="{{ $name }}">@endif

            <div class="card-body">
                <h5 class="card-title">{{ $name }}</h5>
                {{ $slot }}
            </div>
        </div>
    </a>
</div>
