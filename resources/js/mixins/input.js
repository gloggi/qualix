import {get} from 'lodash'

export default {
  props: {
    name: { type: String, required: true },
    value: { type: String, default: '' },
    narrowForm: { type: Boolean, default: false },
  },
  data() {
    return {
      currentValue: get(window.Laravel.oldInput, this.name, this.value)
    }
  },
  computed: {
    errorMessage() {
      return this.errors && this.errors.length ? this.errors[0] : undefined
    },
    errors() {
      return window.Laravel.errors[this.htmlFormName]
    },
    labelClass() {
      return this.narrowForm ? 'col-12' : 'col-md-3 text-md-right'
    },
    inputColumnClass() {
      return this.narrowForm ? 'col-12' : 'col-md-6'
    },
    htmlFormName() {
      return this.name.replace(/\[/g, '.').replace(/]/g, '')
    },
  },
  watch: {
    value() {
      this.currentValue = this.value
    },
    currentValue: {
      handler() { this.$emit('input', this.currentValue) },
      deep: true
    }
  },
}
