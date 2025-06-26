<template>
  <b-modal :id="id" :title="$t('t.global.really_delete')">
    {{ text }}
    <template #modal-footer>
      <form-basic :action="route">
        <b-button type="submit" variant="danger" :disabled="disabled">{{ $t('t.global.delete') }}</b-button>
      </form-basic>
    </template>
  </b-modal>
</template>

<script>

import FormBasic from './FormBasic.vue'
export default {
  name: 'ModalDelete',
  components: {FormBasic},
  props: {
    id: { type: String },
    text: { type: String, required: true },
    route: { type: Array, required: true },
    disabled: { type: Boolean, default: false },
  },
}
</script>

<style scoped>

</style>
