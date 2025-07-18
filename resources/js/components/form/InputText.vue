<template>
  <div class="form-group row" :class="{ required }">
    <label :for="kebabCase(name)" class="col-form-label" :class="labelClass">{{ label }}</label>

    <div :class="inputColumnClass">
      <b-input-group v-if="$slots.append" :class="{ 'is-invalid': errorMessage }">

        <b-form-input
          :type="type"
          :id="kebabCase(name)"
          :name="name"
          :class="{ 'is-invalid': errorMessage }"
          v-model="currentValue"
          :required="required"
          :autofocus="autofocus"
          :v-focus="autofocus" />

        <template #append>
          <slot name="append"></slot>
        </template>

      </b-input-group>

      <input
        v-else
        :type="type"
        :id="kebabCase(name)"
        :name="name"
        class="form-control" :class="{ 'is-invalid': errorMessage }"
        v-model="currentValue"
        :required="required"
        :autofocus="autofocus"
        :v-focus="autofocus"
        ref="input">

      <span v-if="errorMessage" class="invalid-feedback" role="alert">
        <strong>{{ errorMessage }}</strong>
      </span>
    </div>
  </div>
</template>

<script>
import Input from '../../mixins/input.js'

export default {
  name: 'InputText',
  mixins: [ Input ],
  props: {
    required: { type: Boolean, default: false },
    label: { type: String, required: true },
    type: { type: String, default: 'text' },
    autofocus: { type: Boolean, default: false },
  },
  methods: {
    focus() {
      this.$refs.input.focus()
    }
  }
}
</script>

<style scoped>

</style>
