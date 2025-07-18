<template>
  <span class="me-2" :class="iconColor"><i :class="iconClass" :title="status.name"></i></span>
</template>

<script>

export default {
  name: 'RequirementStatus',
  props: {
    value: { type: [Number,String], required: true },
    statuses: { type: Array, required: true },
  },
  computed: {
    status() {
      return this.statuses.find(status => String(status.id) === String(this.value))
    },
    iconClass() {
      return `fas fa-${this.status.icon}`
    },
    iconColor() {
      return `text-${this.status.color}`
    }
  },
}
</script>

<style scoped>

</style>
