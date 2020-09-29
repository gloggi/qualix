<template>
  <div class="form-group row" :class="{ required }">
    <label :for="name | kebabCase" class="col-md-3 col-form-label text-md-right">{{ label }}</label>

    <div class="col-md-6">
      <quali-editor
        :id="name | kebabCase"
        :name="name"
        :class="{ 'form-control': !readonly, 'is-invalid': errorMessage }"
        v-model="currentValue"
        @input="$emit('input', currentValue)"
        :requirements="requirements"
        :quali-requirements="qualiRequirements"
        :readonly="readonly"
        v-bind="$attrs"></quali-editor>

      <span v-if="errorMessage" class="invalid-feedback" role="alert">
        <strong>{{ errorMessage }}</strong>
      </span>
    </div>
  </div>
</template>

<script>
import Input from '../../mixins/input'
import {get} from "lodash"

export default {
  name: 'InputQualiEditor',
  mixins: [ Input ],
  props: {
    label: { type: String, required: true },
    required: { type: Boolean, default: false },
    readonly: { type: Boolean, default: false },
    autofocus: { type: Boolean, default: false },
    value: { type: Object, default: () => {} },
    requirements: { type: Array, required: true },
    selectedRequirements: { type: String, default: '' }
  },
  data() {
    return {
      currentValue: get(window.Laravel.oldInput, this.name, this.value)
    }
  },
  computed: {
    qualiRequirements() {
      if (!this.selectedRequirements) return []
      return this.selectedRequirements.split(',').map(id => parseInt(id))
    }
  }
}
</script>

<style scoped>

</style>
