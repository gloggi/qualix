<template>
  <span>
    <multiselect v-bind="$attrs" @input="onInput" @select="onSelect" v-model="currentValue" label="label" track-by="value" :multiple="multiple" :options="options">
      <template slot="clear" slot-scope="props">
        <div v-if="showDeleteButton" @mousedown.prevent.stop="clear" class="multiselect__clear"></div>
      </template>
      <template slot="noOptions" slot-scope="props">
          <div class="text-secondary">{{ noOptions }}</div>
      </template>
    </multiselect>
    <input type="hidden" :name="name" :value="formValue">
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
    noOptions: String,
    oldValue: String,
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
      currentValue: this.selectedOptions(this.oldValue !== undefined ? this.oldValue : this.value)
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
    selectedOptions(value) {
      if (this.multiple) {
        return value ? this.options.filter(el => value.split(',').includes(el.value)) : []
      } else {
        return value ? (this.options.find(el => el.value === value) || []) : []
      }
    },
    onSelect(option, id) {
      if (this.isGroup(option)) {
        // Right after this select event there will be an input event which includes the group in the selected elements.
        // We wait for one tick until that input event has gone, and then overwrite the value of the multiselect.
        this.$nextTick(() => {
          this.currentValue = this.selectedOptions(option.groupValue)
          this.onInput(this.currentValue, id)
        })
      }
    },
    onInput(val, id) {
      this.$emit('input', val, id)
      // Don't auto-submit if a group was selected.
      // One tick later there will be another input event which will include the group contents.
      if (this.submitOnInput && !this.isGroup(val)) {
        this.$nextTick(() => {
          document.getElementById(this.submitOnInput).submit()
        })
      }
    },
    isGroup(option) {
      return option.groupValue !== undefined
    },
    clear() {
      this.currentValue = []
      this.onInput(this.currentValue, this.$attrs['id'])
    }
  },
  watch: {
    value (newValue, oldValue) {
      this.currentValue = this.selectedOptions(this.value)
    }
  },
  mounted () {
    this.$emit('input', this.currentValue, this.$attrs['id'])
  }
}
</script>

<style scoped>

</style>
