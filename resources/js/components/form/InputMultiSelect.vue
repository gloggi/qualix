<template>
  <div class="form-group row" :class="{ required }">
    <label :for="name | kebabCase" class="col-form-label" :class="labelClass">{{ label }}</label>

    <div :class="inputColumnClass">
      <multi-select
        :id="name | kebabCase"
        class="form-control-multiselect" :class="{ 'is-invalid': errorMessage }"
        :name="name"
        :required="required"
        :autofocus="autofocus"
        :v-focus="autofocus"
        v-model="currentValue"
        :aria-label="label"
        v-bind="$attrs"
        v-on="$listeners">
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
