<div class="form-group row{{ isset($required) && $required ? ' required' : ''}}">
    <label for="{{ $name }}" class="col-md-4 col-form-label text-md-right">{{ $label }}</label>

    <div class="col-md-6">
        <multi-select
            id="{{ $name }}"
            name="{{ $name }}"
            class="form-control-multiselect {{ $errors->has($name) ? ' is-invalid' : '' }}"
            @if (isset($valueBind) && $valueBind)
                :value="{{ $valueBind }}"
            @else
                value="{{ isset($value) ? $value : old($name) }}"
            @endif
            {{ isset($required) && $required ? 'required' : '' }}
            :allow-empty="true"
            {{ isset($autofocus) && $autofocus ? 'autofocus v-focus' : '' }}

            :options="[
                @foreach($options as $option)
                { label: '{{ addslashes($displayFn($option)) }}', value: '{{ addslashes($valueFn($option)) }}' @if (isset($dataFn) && $dataFn), data: {{ $dataFn($option) }}@endif },
                @endforeach
            ]"
            :multiple="{{ ($multiple ?? false) ? 'true' : 'false' }}"
            @if (isset($disabled) && $disabled):disabled="true"@endif
            :close-on-select="true"
            :show-labels="false"
            placeholder=""
            @if (isset($onInput) && $onInput) @input="{{ $onInput }}"@endif></multi-select>

        @if ($errors->has($name))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first($name) }}</strong>
            </span>
        @endif
    </div>
</div>
