<template>
  <div class="form-group row" :class="{ required }">
    <label :for="name | kebabCase" class="col-md-3 col-form-label text-md-right py-0">{{ label }}</label>
    <input-hidden :name="name" :id="name | kebabCase | append('-hidden-reset')" value="0"></input-hidden>
    <div class="col-md-6">
      <div class="my-auto d-flex" :class="{ 'is-invalid': errorMessage }">
        <div v-for="(option, optionValue) in options" class="custom-control custom-radio horizontal-radio">
          <input type="radio" :id="name | kebabCase | append('-' + optionValue)" :name="name" :value="optionValue" class="custom-control-input" :checked="value === optionValue">
          <label class="custom-control-label" :for="name | kebabCase | append('-' + optionValue)">{{ option }}</label>
        </div>
      </div>

      <span class="invalid-feedback" role="alert">
        <strong>{{ errorMessage }}</strong>
      </span>
    </div>
  </div>
</template>

<script>
import InputHidden from "./InputHidden"
export default {
  name: 'InputRadioButton',
  components: { InputHidden },
  props: {
    name: { type: String, required: true },
    required: { type: Boolean, default: false },
    label: { type: String, required: true },
    value: { type: String, default: '' },
    errorMessage: { type: String, required: false },
    options: { type: Object, required: true }
  }
}
</script>

<style scoped>

</style>
