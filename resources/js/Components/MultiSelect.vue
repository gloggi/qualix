<template>
  <span>
    <multiselect v-bind="$attrs" @input="onInput" v-model="currentValue" label="label" track-by="value" :multiple="multiple" :options="options">
      <template slot="clear" slot-scope="props">
        <div v-if="showDeleteButton" @mousedown.prevent.stop="clear" class="multiselect__clear"></div>
      </template></multiselect>
    <input type="hidden" :name="this.name" :value="formValue">
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
    name: String,
    multiple: Boolean,
    value: String,
    options: Array,
    submitOnInput: String,
    showClear: {
      type: Boolean,
      default: false
    },
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
    },
    showDeleteButton() {
      return this.showClear && (!Array.isArray(this.currentValue) || this.currentValue.length)
    }
  },
  methods: {
    initialValue() {
      if (this.multiple) {
        return this.value ? this.options.filter(el => this.value.split(',').includes(el.value)) : []
      } else {
        return this.value ? this.options.find(el => el.value === this.value) : []
      }
    },
    onInput(val, id) {
      this.$emit('input', val, id)
      if (this.submitOnInput) {
        this.$nextTick(() => {
          document.getElementById(this.submitOnInput).submit()
        })
      }
    },
    clear() {
      this.currentValue = []
      this.onInput(this.currentValue, this.$attrs['id'])
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
