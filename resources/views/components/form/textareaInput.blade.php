<div class="form-group row{{ isset($required) && $required ? ' required' : ''}}">
    <label for="{{ $name }}" class="col-md-3 col-form-label text-md-right">{{ $label }}</label>

    <div class="col-md-6">
        <textarea
            id="{{ $name }}"
            name="{{ $name }}"
            class="form-control{{ $errors->has($name) ? ' is-invalid' : '' }}"
            @if (isset($placeholder) && $placeholder)
                placeholder="{{ $placeholder }}"
            @endif
            {{ isset($required) && $required ? 'required' : '' }}
            {{ isset($autofocus) && $autofocus ? 'autofocus v-focus' : '' }}>{{ old($name) ?? $value ?? '' }}</textarea>

        @if ($errors->has($name))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first($name) }}</strong>
            </span>
        @endif
    </div>
</div>
