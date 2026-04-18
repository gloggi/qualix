@php
    if (!isset($params)) $params = [];
@endphp<span>
    <b-button variant="link" v-b-toggle.{{ Str::kebab($id) }} class="text-secondary">
        {{__($key . '.question', $params)}} <i class="fas fa-circle-question"></i>
    </b-button>

    <b-collapse id="{{ Str::kebab($id) }}" class="text-secondary">
        {{__($key . '.answer', $params)}}

        {{ $slot }}
    </b-collapse>
</span>
