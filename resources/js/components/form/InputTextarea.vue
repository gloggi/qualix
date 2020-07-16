<template>
  <div class="form-group row" :class="{ required }">
    <label :for="name | kebabCase" class="col-md-3 col-form-label text-md-right">{{ label }}</label>

    <div class="col-md-6">
        <textarea
          :id="name | kebabCase"
          :name="name"
          class="form-control" :class="{ 'is-invalid': errorMessage }"
          :placeholder="placeholder"
          :required="required"
          :autofocus="autofocus"
          :v-focus="autofocus">{{ value }}</textarea>

      <span v-if="errorMessage" class="invalid-feedback" role="alert">
        <strong>{{ errorMessage }}</strong>
      </span>

      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  name: 'InputTextarea',
  props: {
    name: { type: String, required: true },
    required: { type: Boolean, default: false },
    label: { type: String, required: true },
    value: { type: String, default: '' },
    errorMessage: { type: String, required: false },
    autofocus: { type: Boolean, default: false },
    placeholder: { type: String, required: false }
  },
}
</script>

<style scoped>

</style>
