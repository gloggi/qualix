<template>
  <div class="form-group row" :class="{ required }">
    <label class="col-form-label" :class="labelClass">{{ label }}</label>

    <div :class="inputColumnClass">
      <input-multi-multi-select-entry
        v-for="(menuValue, index) in currentValue"
        :key="index"
        :name="`${name}${index}`"
        :label="label"
        :array-value.sync="currentValue[index]"
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

      <b-btn
        class="px-0"
        variant="link"
        @click="currentValue.push([])"><i class="fas fa-plus mr-1"></i> {{ addMoreLabel }}</b-btn>
    </div>
  </div>
</template>

<script>
import Input from '../../mixins/input'
import InputMultiMultiSelectEntry from './inputMultiMultiSelect/InputMultiMultiSelectEntry'
export default {
  name: 'InputMultiMultiSelect',
  components: {InputMultiMultiSelectEntry},
  mixins: [ Input ],
  props: {
    label: { type: String, required: true },
    value: { type: Array, default: () => [[]] },
    required: { type: Boolean, default: false },
    autofocus: { type: Boolean, default: false },
    addMoreLabel: { type: String, default: function() { return this.$t('t.global.add_more') } },
    requireMultiple: { type: String, default: '' },
  },
}
</script>

<style scoped>

</style>
