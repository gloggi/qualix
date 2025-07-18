<template>
  <div class="form-group row">
    <label :for="kebabCase(name)" class="col-form-label py-0" :class="labelClass">{{ label }}</label>
    <div :class="inputColumnClass">
      <input-hidden :id="kebabCase(name) + '-unchecked-value'" :name="name" model-value="0"></input-hidden>
      <b-form-checkbox
        type="checkbox"
        :id="kebabCase(name)"
        :name="name"
        :class="{ 'is-invalid': errorMessage }"
        value="1"
        unchecked-value="0"
        :state="errorMessage ? false : null"
        v-model="currentValue"
        :switch="isSwitch"
        :size="size"></b-form-checkbox>

      <span v-if="errorMessage" class="invalid-feedback" role="alert">
        <strong>{{ errorMessage }}</strong>
      </span>
    </div>
  </div>
</template>

<script>
import Input from '../../mixins/input.js';
import InputHidden from './InputHidden.vue'
export default {
  name: 'InputCheckbox',
  components: {InputHidden},
  mixins: [ Input ],
  props: {
    label: { type: String, required: true },
    switch: { type: Boolean, default: false },
    size: { type: String },
  },
  data() {
    return {
      isSwitch: this.switch
    }
  },
}
</script>

<style scoped>

</style>
