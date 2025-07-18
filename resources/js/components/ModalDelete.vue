<template>
  <slot name="activator" :openModal="() => setModalOpen(true)"></slot>
  <b-modal :id="id" v-model="modalOpen" :title="$t('t.global.really_delete')">
    {{ text }}
    <template #footer>
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
  data: () => ({
    modalOpen: false,
  }),
  props: {
    id: { type: String },
    text: { type: String, required: true },
    route: { type: Array, required: true },
    disabled: { type: Boolean, default: false },
  },
  methods: {
    setModalOpen(modalOpen = true) {
      this.modalOpen = modalOpen
    }
  }
}
</script>

<style scoped>

</style>
