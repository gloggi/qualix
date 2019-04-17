<div class="form-group row{{ isset($required) && $required ? ' required' : ''}}">
    <label for="{{ $name }}" class="col-md-4 col-form-label text-md-right">{{ $label }}</label>

    <div class="col-md-6">
        <multi-select
            id="{{ $name }}"
            name="{{ $name }}"
            class="{{ $errors->has($name) ? ' is-invalid' : '' }}"
            value="{{ isset($value) ? $value : old($name) }}"
            {{ isset($required) && $required ? 'required' : '' }}
            :allow-empty="{{ isset($required) && $required ? 'false' : 'true' }}"
            {{ isset($autofocus) && $autofocus ? 'autofocus' : '' }}

            :options="[
                @foreach($options as $option)
                    { label: '{{ $displayFn($option) }}', value: '{{ $valueFn($option) }}' },
                @endforeach
            ]"
            :multiple="{{ ($multiple ?? false) ? 'true' : 'false' }}"
            :close-on-select="true"
            placeholder="">

        @if ($errors->has($name))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first($name) }}</strong>
            </span>
        @endif
    </div>
</div>
