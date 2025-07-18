@php
    if (!isset($params)) $params = [];
@endphp<span>
    <b-link href="##" v-b-toggle.{{ Str::kebab($id) }} class="text-secondary">
        {{__($key . '.question', $params)}} <i class="fas fa-circle-question"></i>
    </b-link>

    <b-collapse id="{{ Str::kebab($id) }}" class="text-secondary">
        {{__($key . '.answer', $params)}}

        {{ $slot }}
    </b-collapse>
</span>
