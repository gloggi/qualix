<template>
  <div class="form-group row" :class="{ required }">
    <label class="col-form-label" :class="labelClass">{{ label }}</label>

    <div :class="inputColumnClass">
      <input-multi-multi-select-entry
        v-for="(menuValue, index) in currentValue"
        :key="index"
        :name="`${name}[${index}]`"
        :label="label"
        v-model="currentValue[index]"
        :class="{ 'is-invalid': errorMessage }"
        :require-multiple="requireMultiple"
        @remove="currentValue.splice(index, 1)"
        v-bind="$attrs">
        <template #option="props">
          <slot name="option" v-bind="props"></slot>
        </template>
        <template #single-label="props">
          <slot name="single-label" v-bind="props"></slot>
        </template>
      </input-multi-multi-select-entry>

      <span v-if="errorMessage" class="invalid-feedback" role="alert">
        <strong>{{ errorMessage }}</strong>
      </span>

      <b-button
        class="px-0"
        variant="link"
        @click="currentValue.push('')"><i class="fas fa-plus me-1"></i> {{ addMoreLabel || $t('t.global.add_more') }}</b-button>
    </div>
  </div>
</template>

<script>
import Input from '../../mixins/input.js'
import InputMultiMultiSelectEntry from './inputMultiMultiSelect/InputMultiMultiSelectEntry.vue'
export default {
  name: 'InputMultiMultiSelect',
  components: {InputMultiMultiSelectEntry},
  mixins: [ Input ],
  props: {
    label: { type: String, required: true },
    modelValue: { type: Array, default: () => [[]] },
    required: { type: Boolean, default: false },
    autofocus: { type: Boolean, default: false },
    addMoreLabel: { type: String, default: null },
    requireMultiple: { type: String, default: '' },
  },
  emits: ['update:modelValue'],
}
</script>

<style scoped>

</style>
