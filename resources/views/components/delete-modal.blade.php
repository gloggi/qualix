<b-modal id="{{ $id }}" title="{{ __('t.global.really_delete') }}">
    {{ $text }}
    <template #modal-footer>
        <form-basic :action="{{ json_encode($route) }}">
            <b-button type="submit" variant="danger">{{ __('t.global.delete') }}</b-button>
        </form-basic>
    </template>
</b-modal>
