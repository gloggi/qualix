<div class="form-group row">
    <label for="{{ $name }}" class="col-md-3 col-form-label text-md-right">{{ $label }}</label>
    @component('components.form.hiddenInput', ['name' => $name, 'id' => $name . '-hidden-reset', 'value' => '0'])@endcomponent
    <div class="col-md-6 d-flex">
        <div class="custom-control custom-checkbox align-self-center">
            <input
              type="checkbox"
              id="{{ $name }}"
              name="{{ $name }}"
              class="custom-control-input{{ $errors->has($name) ? ' is-invalid' : '' }}"
              value="1"
              {{ (old($name) ?? $value ?? false) ? 'checked' : '' }}>
            <label class="custom-control-label" for="{{ $name }}"></label>
        </div>

        @if ($errors->has($name))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first($name) }}</strong>
            </span>
        @endif
    </div>
</div>
