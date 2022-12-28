<template>
  <div class="form-group row" :class="{ required }">
    <label class="col-form-label" :class="labelClass">{{ label }}</label>

    <div :class="inputColumnClass">
      <div
        v-for="(singleValue, index) in currentValue"
        :key="index"
        class="form-control-multiselect d-flex mb-1"
        :class="{ 'is-invalid': errorMessage }">
        <multi-select
          :id="`${name}-${index}` | kebabCase"
          class="form-control-multiselect flex-grow-1" :class="{ 'is-invalid': errorMessage }"
          :name="name"
          :aria-label="label"
          :value="singleValue.join(',')"
          multiple
          @input="(val) => onInput(index, val)"
          v-bind="$attrs">
          <template #option="props">
            <slot name="option" v-bind="props"></slot>
          </template>
          <template #single-label="props">
            <slot name="single-label" v-bind="props"></slot>
          </template>
        </multi-select>
        <b-button
          variant="link"
          class="text-danger"
          @click="currentValue.splice(index, 1)"
          :title="$t('t.global.remove')"
          :aria-label="$t('t.global.remove')">
          <i class="fas fa-circle-minus"></i>
        </b-button>
      </div>

      <span v-if="true || errorMessage" class="invalid-feedback" role="alert">
        <strong>error: {{ errorMessage }}</strong>
      </span>

      <b-btn
        class="px-0"
        variant="link"
        @click="currentValue.push([])"><i class="fas fa-plus mr-1"></i> {{ $t('t.global.add_more') }}</b-btn>
    </div>
  </div>
</template>

<script>
import Input from '../../mixins/input'
export default {
  name: 'InputMultiSelect',
  mixins: [ Input ],
  props: {
    label: { type: String, required: true },
    value: { type: Array, default: () => [[]] },
    required: { type: Boolean, default: false },
    autofocus: { type: Boolean, default: false }
  },
  data: () => ({
    log: 'input-multi-multi-select',
  }),
  methods: {
    onInput(index, val) {
      if (val === '') {
        this.currentValue[index] = []
      } else {
        this.currentValue[index] = val.split(',')
      }
    }
  }
}
</script>

<style scoped>

</style>
