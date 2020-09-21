<template>
  <b-dropdown v-if="editable" split class="mr-2" :variant="'outline-' + iconVariant"><template v-slot:button-content>
    <i :class="iconClass"></i>
  </template><b-dropdown-item-button @click="setPassed(1)"><i class="text-success fas fa-check-circle mr-3"></i> {{ $t('t.views.quali_content.requirements.passed') }}</b-dropdown-item-button><b-dropdown-item-button @click="setPassed(null)"><i class="text-primary fas fa-binoculars mr-3"></i> {{ $t('t.views.quali_content.requirements.observing') }}</b-dropdown-item-button><b-dropdown-item-button @click="setPassed(0)"><i class="text-danger fas fa-times-circle mr-3"></i> {{ $t('t.views.quali_content.requirements.failed') }}</b-dropdown-item-button></b-dropdown>
  <span v-else class="mr-2" :class="'text-' + iconVariant"><i :class="iconClass"></i></span>
</template>

<script>

export default {
  name: 'RequirementMenu',
  props: {
    name: { type: String, required: true },
    value: { required: true, validator: prop => [null, 0, 1].includes(prop) },
    editable: { type: Boolean, default: false },
  },
  computed: {
    passed() {
      return this.value === 1
    },
    failed() {
      return this.value === 0
    },
    observing() {
      return this.value === null
    },
    iconClass() {
      if (this.passed) return 'fas fa-check-circle'
      if (this.failed) return 'fas fa-times-circle'
      return 'fas fa-binoculars'
    },
    iconVariant() {
      if (this.passed) return 'success'
      if (this.failed) return 'danger'
      return 'primary'
    }
  },
  methods: {
    setPassed(passed) {
      this.$emit('input', passed)
    }
  }
}
</script>

<style scoped>

</style>
