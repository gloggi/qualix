<b-modal id="{{ $id }}" title="{{ __('t.global.really_delete') }}">
    {{ $text }}
    <template #modal-footer>
        @component('components.form', ['method' => 'DELETE', 'route' => $route])
            @csrf
            <b-button type="submit" variant="danger">{{ __('t.global.delete') }}</b-button>
        @endcomponent
    </template>
</b-modal>
