<div class="form-group row{{ isset($required) && $required ? ' required' : ''}}">
    <label for="{{ $name }}" class="col-md-3 col-form-label text-md-right">{{ $label }}</label>

    <div class="col-md-6">
        <multi-select
            id="{{ $name }}"
            name="{{ $name }}"
            class="form-control-multiselect {{ $errors->has($name) ? ' is-invalid' : '' }}"
            @if (old($name) !== null)
                old-value="{{ old($name) }}"
            @endif
            @if (isset($valueBind) && $valueBind)
                :value="{{ $valueBind }}"
            @else
                @if (!Arr::has(old(), $name))
                    value="{{ $value ?? '' }}"
                @endif
            @endif
            {{ isset($required) && $required ? 'required' : '' }}
            :allow-empty="true"
            {{ isset($autofocus) && $autofocus ? 'autofocus v-focus' : '' }}

            @php
                $dataFn = $dataFn ?? function($option) { return null; };
                $jsonOptions = array_map(function($option) use($displayFn, $valueFn, $dataFn) {
                    return ['label' => (string)$displayFn($option), 'value' => (string)$valueFn($option), 'data' => $dataFn($option)];
                }, $options);

                if (isset($groups) && $groups) {
                    $jsonOptions = array_merge($jsonOptions, array_map(function($groupName, $options) use($valueFn) {
                        $values = array_map(function($option) use($valueFn) { return (string)$valueFn($option); }, $options);
                        return ['label' => (string)$groupName, 'groupValue' => implode(',', $values)];
                    }, array_keys($groups), $groups));
                }
            @endphp
            :options="{{ json_encode($jsonOptions) }}"
            @if (isset($groups) && $groups)
                :show-clear="true"
            @endif

            :multiple="{{ ($multiple ?? false) ? 'true' : 'false' }}"
            @if (isset($disabled) && $disabled):disabled="true"@endif
            :close-on-select="true"
            :show-labels="false"
            placeholder=""
            no-options="{{__('t.global.no_options')}}"
            @if (isset($onInput) && $onInput) @input="{{ $onInput }}" @endif></multi-select>

        @if ($errors->has($name))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first($name) }}</strong>
            </span>
        @endif
    </div>
</div>
