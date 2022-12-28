<template>
  <span>
    <div
      class="form-control-multiselect d-flex mb-1"
      :class="{ 'is-invalid': errorMessage }">
      <multi-select
        :id="name | kebabCase"
        class="form-control-multiselect flex-grow-1" :class="{ 'is-invalid': errorMessage }"
        :name="name"
        :aria-label="label"
        v-model="currentValue"
        multiple
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
        @click="$emit('remove')"
        :title="$t('t.global.remove')"
        :aria-label="$t('t.global.remove')">
        <i class="fas fa-circle-minus"></i>
      </b-button>
    </div>

    <span v-if="errorMessage" class="invalid-feedback" role="alert">
      <strong>{{ errorMessage }}</strong>
    </span>
  </span>
</template>

<script>
import Input from '../../../mixins/input'
export default {
  name: 'InputMultiMultiSelectEntry',
  mixins: [ Input ],
  props: {
    label: { type: String, required: true },
    arrayValue: { type: Array, default: () => [[]]},
    requireMultiple: { type: String, default: '' },
  },
  computed: {
    errorMessage() {
      if (this.localErrorMessage) return this.localErrorMessage
      const errors = window.Laravel.errors[this.name]
      return errors && errors.length ? errors[0] : undefined
    },
    localErrorMessage() {
      if (!this.requireMultiple) return undefined
      if (!this.arrayValue) return undefined
      return this.arrayValue.length === 1 ? this.requireMultiple : undefined
    },
  },
  watch: {
    currentValue() {
      this.$emit('update:arrayValue', this.currentValue ? this.currentValue.split(',') : [])
    },
    arrayValue: {
      deep: true,
      handler(newArrayValue) {
        this.currentValue = newArrayValue.join(',')
      },
    },
  },
}
</script>

<style scoped>

</style>
