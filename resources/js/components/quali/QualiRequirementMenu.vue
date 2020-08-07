<template>
  <div class="btn-group btn-group-toggle" role="group">
    <button type="button" @click="setPassed(0)" class="btn" :class="failed ? 'btn-danger' : 'btn-outline-secondary'"><i class="fas fa-times-circle"></i></button>
    <button type="button" @click="setPassed(null)" class="btn" :class="observing ? 'btn-primary' : 'btn-outline-secondary'"><i class="fas fa-binoculars"></i></button>
    <button type="button" @click="setPassed(1)" class="btn" :class="passed ? 'btn-success' : 'btn-outline-secondary'"><i class="fas fa-check-circle"></i></button>
  </div>
</template>

<script>

export default {
  name: 'QualiRequirementMenu',
  props: {
    value: Object,
  },
  computed: {
    passed() {
      return this.value.passed === 1
    },
    failed() {
      return this.value.passed === 0
    },
    observing() {
      return this.value.passed === null
    }
  },
  methods: {
    setPassed(passed) {
      this.value.passed = passed
    }
  }
}
</script>

<style scoped>

</style>
