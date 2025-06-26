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
import Input from '../../../mixins/input.js'
import MultiSelect from '../../MultiSelect.vue'
export default {
  name: 'InputMultiMultiSelectEntry',
  components: {MultiSelect},
  mixins: [ Input ],
  props: {
    label: { type: String, required: true },
    requireMultiple: { type: String, default: '' },
  },
  computed: {
    errorMessage() {
      if (this.localErrorMessage) return this.localErrorMessage
      return this.errors && this.errors.length ? this.errors[0] : undefined
    },
    localErrorMessage() {
      if (!this.requireMultiple) return undefined
      if (!this.currentValue) return undefined
      return this.currentValue.split(',').length === 1 ? this.requireMultiple : undefined
    },
  },
}
</script>

<style scoped>

</style>
