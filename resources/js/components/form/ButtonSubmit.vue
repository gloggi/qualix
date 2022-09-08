<template>
  <div class="form-group row mb-0">
    <div class="col-md-8 offset-md-3">
      <button type="submit" class="btn btn-primary mr-3 mb-1" v-on="$listeners">
        {{ label }}
      </button>

      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ButtonSubmit',
  props: {
    label: { type: String, default: function() { return this.$t('t.global.save') } },
  },
}
</script>

<style scoped>

</style>
