<template>
  <input
    type="hidden"
    :id="kebabCase(id != null ? id : name)"
    :name="name"
    :value="modelValue" />
</template>

<script>
import kebabCase from 'lodash/kebabCase';
export default {
  name: 'InputHidden',
  props: {
    id: { type: String, required: false },
    name: { type: String, required: true },
    modelValue: { type: String, default: '' },
  },
  methods: { kebabCase },
}
</script>

<style scoped>

</style>
