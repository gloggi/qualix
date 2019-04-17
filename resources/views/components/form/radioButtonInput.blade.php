<div class="form-group row{{ isset($required) && $required ? ' required' : ''}}">
    <label for="{{ $name }}" class="col-md-4 col-form-label text-md-right">{{ $label }}</label>
    @component('components.form.hiddenInput', ['name' => $name, 'id' => $name . '-hidden-reset', 'value' => '0'])@endcomponent
    <div class="col-md-6 d-flex">
        <div class="my-auto">
            @foreach($options as $optionValue => $option)
                <div class="custom-control custom-radio horizontal-radio">
                    <input type="radio" id="{{ $name . $optionValue }}" name="{{ $name }}" value="{{ $optionValue }}" class="custom-control-input"{{ ((isset($value) && $value == $optionValue) || (!isset($value) && old($name) && old($name) == $optionValue)) ? ' checked' : '' }}>
                    <label class="custom-control-label" for="{{ $name . $optionValue }}">{{ $option }}</label>
                </div>
            @endforeach
        </div>

        @if ($errors->has($name))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first($name) }}</strong>
            </span>
        @endif
    </div>
</div>
