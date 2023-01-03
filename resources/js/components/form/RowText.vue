<template>
  <div class="form-group row">
    <div :class="inputColumnClass">
      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RowText',
  props: {
    narrowForm: { type: Boolean, default: false },
  },
  computed: {
    inputColumnClass() {
      return this.narrowForm ? 'col-12' : 'col-md-8 offset-md-3'
    }
  }
}
</script>

<style scoped>

</style>
