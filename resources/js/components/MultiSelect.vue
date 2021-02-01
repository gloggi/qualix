<template>
  <span>
    <vue-multiselect
      ref="multiselect"
      v-bind="$attrs"
      @input="onInput"
      @select="onSelect"
      v-model="localValue"
      :label="displayField"
      :track-by="valueField"
      :multiple="multiple"
      :options="allOptions"
      :close-on-select="true"
      :show-labels="false"
      :placeholder="placeholder"
      :no-options="$t('t.global.no_options')">

      <template slot="clear">
        <div v-if="showClearButton" @mousedown.prevent.stop="clear" class="multiselect__clear"></div>
      </template>
      <template slot="noOptions">
        <div class="text-secondary">{{ noOptions }}</div>
      </template>

    </vue-multiselect>
    <input v-if="name" type="hidden" :name="name" :value="formValue">
  </span>
</template>

<script>
import { Multiselect as VueMultiselect } from 'vue-multiselect'

export default {
  name: 'MultiSelect',
  components: {
    VueMultiselect
  },
  props: {
    name: { type: String, required: false },
    multiple: { type: Boolean, default: false },
    noOptions: { type: String, required: false },
    value: { type: String, default: '' },
    selected: { type: [Array,Object], default: null },
    valueField: { type: String, default: 'id' },
    displayField: { type: String, default: 'label' },
    options: { type: Array, default: () => [] },
    groups: { type: Object, default: () => ({}) },
    submitOnInput: { type: String, required: false },
    showClear: { type: Boolean, default: false },
    placeholder: { type: String, default: '' },
    autofocus: { type: Boolean, default: false }
  },
  data() {
    return {
      localValue: this.selected !== null ? this.selected : this.parse(this.value)
    }
  },
  computed: {
    allOptions() {
      return this.options.concat(this.groupOptions)
    },
    groupOptions() {
      return Object.entries(this.groups).map(([groupName, groupValue]) => ({ [this.displayField]: groupName, [this.valueField]: groupValue, _isGroup: true }))
    },
    formValue() {
      if (this.multiple) {
        return this.localValue.map(option => option[this.valueField]).join()
      } else {
        if (this.localValue == null) return ''
        return '' + this.localValue[this.valueField]
      }
    },
    showClearButton() {
      return (this.showClear || this.groupOptions.length !== 0) && this.formValue !== ''
    }
  },
  methods: {
    parse(value) {
      if (this.multiple) {
        return value ? this.options.filter(el => value.split(',').includes('' + el[this.valueField])) : []
      } else {
        return value ? (this.options.find(el => '' + el[this.valueField] === value)) : null
      }
    },
    onSelect(option, id) {
      if (this.isGroup(option)) {
        // Right after this select event there will be an input event which includes the group in the selected elements.
        // We wait for one tick until that input event has gone, and then overwrite the value of the multiselect.
        this.$nextTick(() => {
          this.localValue = this.parse(option[this.valueField])
          this.onInput(this.localValue, id)
        })
      }
    },
    onInput(val, id) {
      this.$emit('input', this.formValue, id)
      this.$emit('update:selected', this.localValue, id)

      // Work around :close-on-select=false not working correctly
      if (this.multiple) this.$nextTick(() => this.$refs.multiselect.activate())

      // Don't auto-submit if a group was selected.
      // One tick later there will be another input event which will include the group contents.
      if (this.submitOnInput && !this.isGroup(val)) {
        this.$nextTick(() => {
          document.getElementById(this.submitOnInput).submit()
        })
      }
    },
    isGroup(val) {
      return val && val._isGroup
    },
    clear() {
      this.localValue = this.multiple ? [] : null
      this.onInput(this.localValue, this.$attrs['id'])
    }
  },
  watch: {
    value () {
      this.localValue = this.selected !== null ? this.selected : this.parse(this.value)
    },
    selected () {
      this.localValue = this.selected !== null ? this.selected : this.parse(this.value)
    },
  },
  mounted () {
    this.$emit('input', this.formValue, this.$attrs['id'])

    if (this.autofocus) {
      this.$refs.multiselect.activate()
    }
  }
}
</script>

<style scoped>

</style>
