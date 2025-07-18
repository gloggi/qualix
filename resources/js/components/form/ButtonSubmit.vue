<template>
  <div class="form-group row mb-0">
    <div :class="inputColumnClass">
      <button type="submit" class="btn btn-primary me-3 mb-1" :disabled="disabled" :value="value" v-bind="$attrs">
        {{ label || $t('t.global.save') }}
      </button>

      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ButtonSubmit',
  inheritAttrs: false,
  props: {
    label: { type: String, default: null },
    value: { default: null },
    disabled: { type: Boolean, default: false },
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
