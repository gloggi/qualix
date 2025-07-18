import get from 'lodash/get'
import kebabCase from 'lodash/kebabCase';

export default {
  props: {
    name: { type: String, required: true },
    modelValue: { type: String, default: '' },
    narrowForm: { type: Boolean, default: false },
  },
  emits: ['update:modelValue'],
  data() {
    return {
      currentValue: get(window.Laravel.oldInput, this.name, this.modelValue)
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
      return this.narrowForm ? 'col-12' : 'col-md-3 text-md-end'
    },
    inputColumnClass() {
      return this.narrowForm ? 'col-12' : 'col-md-6'
    },
    htmlFormName() {
      return this.name.replace(/\[/g, '.').replace(/]/g, '')
    },
  },
  methods: { kebabCase },
  watch: {
    modelValue() {
      this.currentValue = this.modelValue
    },
    currentValue: {
      handler() {
        this.$emit('update:modelValue', this.currentValue, this.id)
      },
      deep: true
    }
  },
}
