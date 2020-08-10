<template>
  <div class="form-group row" :class="{ required }">
    <label :for="name | kebabCase" class="col-md-3 col-form-label text-md-right py-0">{{ label }}</label>
    <div class="col-md-6">
      <div class="my-auto d-flex" :class="{ 'is-invalid': errorMessage }">
        <b-form-radio v-for="(option, optionValue) in options"
                      :key="name | append('-' + optionValue)"
                      :id="name | append('-' + optionValue) | kebabCase"
                      :name="name"
                      class="horizontal-radio"
                      :value="optionValue"
                      v-model="currentValue">{{ option }}</b-form-radio>
      </div>

      <span class="invalid-feedback" role="alert">
        <strong>{{ errorMessage }}</strong>
      </span>
    </div>
  </div>
</template>

<script>
import InputHidden from './InputHidden'
import Input from '../../mixins/input'
export default {
  name: 'InputRadioButton',
  mixins: [ Input ],
  components: { InputHidden },
  props: {
    required: { type: Boolean, default: false },
    label: { type: String, required: true },
    options: { type: Object, required: true }
  }
}
</script>

<style scoped>

</style>
