<template>
  <input
    type="hidden"
    :id="(id != null ? id : name) | kebabCase"
    :name="name"
    :value="value" />
</template>

<script>
export default {
  name: 'InputHidden',
  props: {
    id: { type: String, required: false },
    name: { type: String, required: true },
    value: { type: String, default: '' },
  },
}
</script>

<style scoped>

</style>
