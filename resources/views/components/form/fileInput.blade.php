<div class="form-group row{{ isset($required) && $required ? ' required' : ''}}">
    <label for="{{ $name }}" class="col-md-3 col-form-label text-md-right">{{ $label }}</label>

    <div class="col-md-6">
        <input
                type="file"
                id="{{ $name }}"
                name="{{ $name }}"
                accept="{{  $accept }}"
                class="form-control{{ $errors->has($name) ? ' is-invalid' : '' }}"
                {{ isset($required) && $required ? 'required' : '' }}
                {{ isset($autofocus) && $autofocus ? 'autofocus v-focus' : '' }}>

        @if ($errors->has($name))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first($name) }}</strong>
            </span>
        @endif
    </div>
</div>
