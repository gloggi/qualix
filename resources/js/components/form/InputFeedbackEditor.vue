<template>
  <div class="form-group row" :class="{ required }">
    <label :for="name | kebabCase" class="col-form-label" :class="labelClass">{{ label }}</label>

    <div :class="inputColumnClass">
      <feedback-editor
        :id="name | kebabCase"
        :name="name"
        ref="feedbackEditor"
        :class="{ 'form-control': !readonly, 'is-invalid': errorMessage }"
        v-model="currentValue"
        @input="$emit('input', currentValue)"
        :requirements="requirements"
        :feedback-requirements="feedbackRequirements"
        :readonly="readonly"
        v-bind="$attrs"></feedback-editor>

      <span v-if="errorMessage" class="invalid-feedback" role="alert">
        <strong>{{ errorMessage }}</strong>
      </span>
    </div>
  </div>
</template>

<script>
import Input from '../../mixins/input'
import {get} from 'lodash'

export default {
  name: 'InputFeedbackEditor',
  mixins: [ Input ],
  props: {
    label: { type: String, required: true },
    required: { type: Boolean, default: false },
    readonly: { type: Boolean, default: false },
    value: { type: Object, default: () => {} },
    requirements: { type: Array, required: true },
    selectedRequirements: { type: String, default: '' }
  },
  data() {
    return {
      currentValue: JSON.parse(get(window.Laravel.oldInput, this.name, 'false')) || this.value
    }
  },
  computed: {
    feedbackRequirements() {
      if (!this.selectedRequirements) return []
      return this.selectedRequirements.split(',').map(id => parseInt(id))
    }
  },
  methods: {
    focus() {
      this.$refs.feedbackEditor.focus()
    }
  }
}
</script>

<style scoped>

</style>
