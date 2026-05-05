<template>
  <div class="form-group row" :class="{ required }">
    <label :for="kebabCase(name)" class="col-form-label" :class="labelClass">{{ label }}</label>

    <div :class="inputColumnClass">
      <multi-select
        :id="kebabCase(name)"
        class="form-control-multiselect" :class="{ 'is-invalid': errorMessage }"
        :name="name"
        :required="required"
        :autofocus="autofocus"
        :v-focus="autofocus"
        v-model="currentValue"
        :aria-label="label"
        v-bind="$attrs">
        <template #option="props">
          <slot name="option" v-bind="props"></slot>
        </template>
        <template #single-label="props">
          <slot name="single-label" v-bind="props"></slot>
        </template>
      </multi-select>

      <span v-if="errorMessage" class="invalid-feedback" role="alert">
        <strong>{{ errorMessage }}</strong>
      </span>

      <slot name="below" :value="currentValue"></slot>
    </div>
  </div>
</template>

<script>
import Input from '../../mixins/input.js'
import MultiSelect from '../MultiSelect.vue'
export default {
  name: 'InputMultiSelect',
  inheritAttrs: false,
  components: {MultiSelect},
  mixins: [ Input ],
  props: {
    label: { type: String, required: true },
    required: { type: Boolean, default: false },
    autofocus: { type: Boolean, default: false }
  }
}
</script>

<style scoped>

</style>
