<template>
  <span class="mr-2" :class="iconColor"><i :class="iconClass"></i></span>
</template>

<script>

export default {
  name: 'RequirementStatus',
  props: {
    name: { type: String, required: true },
    value: { type: Number, required: true },
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
