<div class="form-group row">
    <label for="{{ Str::kebab($name) }}" class="col-md-3 col-form-label text-md-right">{{ $label }}</label>
    @component('components.form.hiddenInput', ['name' => Str::kebab($name), 'id' => Str::kebab($name) . '-hidden-reset', 'value' => '0'])@endcomponent
    <div class="col-md-6 d-flex">
        <div class="custom-control custom-checkbox align-self-center">
            <input
              type="checkbox"
              id="{{ Str::kebab($name) }}"
              name="{{ $name }}"
              class="custom-control-input @error($name) is-invalid @enderror"
              value="1"
              {{ (old($name) ?? $value ?? false) ? 'checked' : '' }}>
            <label class="custom-control-label" for="{{ Str::kebab($name) }}"></label>
        </div>

        @error($name)
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
