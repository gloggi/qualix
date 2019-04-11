<div class="form-group row{{ isset($required) && $required ? ' required' : ''}}">
    <label for="{{ $name }}" class="col-md-4 col-form-label text-md-right">{{ $label }}</label>

    <div class="col-md-6">
        <date-picker
            type="{{ $type ?? 'text' }}"
            id="{{ $name }}"
            name="{{ $name }}"
            class="form-control{{ $errors->has($name) ? ' is-invalid' : '' }}"
            value="{{ isset($value) ? $value : old($name) }}"
            {{ isset($required) && $required ? 'required' : '' }}
            {{ isset($autofocus) && $autofocus ? 'autofocus' : '' }}
            :config="{ format: 'DD.MM.YYYY', useCurrent: false, locale: '{{__('de-ch')}}' }">

        @if ($errors->has($name))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first($name) }}</strong>
            </span>
        @endif
    </div>
</div>
