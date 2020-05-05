<div class="form-group row{{ isset($required) && $required ? ' required' : ''}}">
    <label for="{{ Str::kebab($name) }}" class="col-md-3 col-form-label text-md-right">{{ $label }}</label>

    <div class="col-md-6">
        <input
                type="file"
                id="{{ Str::kebab($name) }}"
                name="{{ $name }}"
                accept="{{ $accept }}"
                class="form-control @error($name) is-invalid @enderror"
                {{ isset($required) && $required ? 'required' : '' }}
                {{ isset($autofocus) && $autofocus ? 'autofocus v-focus' : '' }}>

        @error($name)
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @endif
    </div>
</div>
