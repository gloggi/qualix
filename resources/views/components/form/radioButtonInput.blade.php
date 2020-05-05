<div class="form-group row{{ isset($required) && $required ? ' required' : ''}}">
    <label for="{{ Str::kebab($name) }}" class="col-md-3 col-form-label text-md-right">{{ $label }}</label>
    @component('components.form.hiddenInput', ['name' => $name, 'id' => Str::kebab($name) . '-hidden-reset', 'value' => '0'])@endcomponent
    <div class="col-md-6 d-flex">
        <div class="my-auto">
            @foreach($options as $optionValue => $option)
                <div class="custom-control custom-radio horizontal-radio">
                    <input type="radio" id="{{ Str::kebab($name) . '-' . $optionValue }}" name="{{ $name }}" value="{{ $optionValue }}" class="custom-control-input"{{ ((old($name) ?? $value ?? null) == $optionValue) ? ' checked' : '' }}>
                    <label class="custom-control-label" for="{{ Str::kebab($name) . '-' . $optionValue }}">{{ $option }}</label>
                </div>
            @endforeach
        </div>

        @error($name)
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
