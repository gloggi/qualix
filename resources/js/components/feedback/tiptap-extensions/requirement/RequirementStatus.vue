<template>
  <span class="mr-2" :class="'text-' + iconVariant"><i :class="iconClass"></i></span>
</template>

<script>

export default {
  name: 'RequirementStatus',
  props: {
    name: { type: String, required: true },
    value: { required: true, validator: prop => [null, 0, 1].includes(prop) },
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
}
</script>

<style scoped>

</style>
