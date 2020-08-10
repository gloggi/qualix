import { get } from 'lodash'

export default {
  props: {
    name: { type: String, required: true },
    value: { type: String, default: '' },
  },
  data() {
    return {
      currentValue: get(window.oldInput, this.name, this.value)
    }
  },
  computed: {
    errorMessage() {
      const errors = window.errors[this.name]
      return errors && errors.length ? errors[0] : undefined;
    }
  },
  watch: {
    value() {
      this.currentValue = this.value
    },
    currentValue: {
      handler() { this.$emit('input', this.currentValue) },
      deep: true
    }
  }
}
