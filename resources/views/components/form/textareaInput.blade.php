<div class="form-group row{{ isset($required) && $required ? ' required' : ''}}">
    <label for="{{ Str::kebab($name) }}" class="col-md-3 col-form-label text-md-right">{{ $label }}</label>

    <div class="col-md-6">
        <textarea
            id="{{ Str::kebab($name) }}"
            name="{{ $name }}"
            class="form-control @error($name) is-invalid @enderror"
            @if (isset($placeholder) && $placeholder)
                placeholder="{{ $placeholder }}"
            @endif
            {{ isset($required) && $required ? 'required' : '' }}
            {{ isset($autofocus) && $autofocus ? 'autofocus v-focus' : '' }}>{{ old($name) ?? $value ?? '' }}</textarea>

        @error($name)
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror

        {{ $slot }}
    </div>
</div>
