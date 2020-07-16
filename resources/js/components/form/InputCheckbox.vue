<template>
  <div class="form-group row">
    <label :for="name | kebabCase" class="col-md-3 col-form-label text-md-right py-0">{{ label }}</label>
    <input-hidden :name="name" :id="name | kebabCase | resetId" value="0"></input-hidden>
    <div class="col-md-6">
      <div class="custom-control custom-checkbox" :class="{ 'is-invalid': errorMessage }">
        <input
          type="checkbox"
          :id="name | kebabCase"
          :name="name"
          class="custom-control-input" :class="{ 'is-invalid': errorMessage }"
          value="1"
          :checked="value">
        <label class="custom-control-label" :for="name | kebabCase"></label>
      </div>

      <span v-if="errorMessage" class="invalid-feedback" role="alert">
        <strong>{{ errorMessage }}</strong>
      </span>
    </div>
  </div>
</template>

<script>
import InputHidden from "./InputHidden";
export default {
  name: 'InputCheckbox',
  components: { InputHidden },
  props: {
    name: { type: String, required: true },
    label: { type: String, required: true },
    value: { type: Boolean, default: false },
    errorMessage: { type: String, required: false },
  },
  filters: {
    resetId (normalId) {
      return normalId + '-hidden-reset'
    }
  }
}
</script>

<style scoped>

</style>
