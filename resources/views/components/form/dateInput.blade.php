<div class="form-group row{{ isset($required) && $required ? ' required' : ''}}">
    <label for="{{ Str::kebab($name) }}" class="col-md-3 col-form-label text-md-right">{{ $label }}</label>

    <div class="col-md-6">
        <b-form-datepicker
            id="{{ Str::kebab($name) }}"
            name="{{ $name }}"
            class="form-control @error($name) is-invalid @enderror"
            value="{{ old($name) ?? $value->format('Y-m-d') ?? '' }}"
            {{ isset($required) && $required ? 'required' : '' }}
            {{ isset($autofocus) && $autofocus ? 'autofocus v-focus' : '' }}
            locale="{{ App::getLocale() }}"
            :date-format-options="{ year: 'numeric', month: '2-digit', day: '2-digit' }"
            hide-header
            label-help=""></b-form-datepicker>

        @error($name)
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
