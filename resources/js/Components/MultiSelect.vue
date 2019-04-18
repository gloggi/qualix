<template>
  <span>
    <multiselect v-bind="$attrs" @input="(val, id) => $emit('input', val, id)" v-model="currentValue" label="label" track-by="value" :show-labels="false" :multiple="multiple" :options="options"></multiselect>
    <input type="hidden" :name="$attrs['name']" :value="formValue">
  </span>
</template>

<script>
import { Multiselect } from 'vue-multiselect'

export default {
  name: 'MultiSelect',
  components: {
    Multiselect
  },
  props: {
    multiple: Boolean,
    value: String,
    options: Array,
  },
  data: function() {
    return {
      currentValue: this.initialValue()
    }
  },
  computed: {
    formValue() {
      if (this.multiple) {
        return this.currentValue.map(option => option.value).join(',')
      } else {
        return this.currentValue.value
      }
    }
  },
  methods: {
    initialValue() {
      if (this.multiple) {
        return this.value ? this.options.filter(el => this.value.split(',').includes(el.value)) : []
      } else {
        return this.value ? this.options.find(el => el.value === this.value) : {}
      }
    }
  },
  watch: {
    value (newValue, oldValue) {
      this.currentValue = this.initialValue()
    }
  },
  mounted () {
    this.$emit('input', this.currentValue, this.$attrs['id'])
  }
}
</script>

<style scoped>

</style>
