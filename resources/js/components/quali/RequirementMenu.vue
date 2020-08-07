<template>
  <div class="btn-group btn-group-toggle" role="group">
    <button type="button" @click="setPassed(0)" class="btn" :class="failed ? 'btn-danger' : 'btn-outline-secondary'"><i class="fas fa-times-circle"></i></button>
    <button type="button" @click="setPassed(null)" class="btn" :class="observing ? 'btn-primary' : 'btn-outline-secondary'"><i class="fas fa-binoculars"></i></button>
    <button type="button" @click="setPassed(1)" class="btn" :class="passed ? 'btn-success' : 'btn-outline-secondary'"><i class="fas fa-check-circle"></i></button>
  </div>
</template>

<script>

export default {
  name: 'RequirementMenu',
  props: {
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
