<template>
  <div class="form-group row" :class="{ required }">
    <label :for="kebabCase(name)" class="col-form-label" :class="labelClass">{{ label }}</label>

    <div :class="inputColumnClass">
      <template v-for="(split, index) in currentValue">
        <input-group-split
          :name="`${name}[${index}]`"
          :participants="participants"
          :any-duplicate-membership-groups="anyDuplicateMembershipGroups"
          v-model="currentValue[index]"
          :deletable="currentValue.length > 1"
          @remove="$emit('remove-group-split', split.id)"></input-group-split>
      </template>
      <b-button
        class="px-0"
        variant="link"
        @click="$emit('add-group-split')"><i class="fas fa-plus me-1"></i> {{ $t('t.views.admin.participant_group_generator.add_group_split') }}</b-button>
    </div>
  </div>
</template>

<script>
import Input from '../../mixins/input.js'
import InputGroupSplit from './InputGroupSplit.vue'
import validSplit from './validSplit.js'

export default {
  name: 'InputGroupSplits',
  components: { InputGroupSplit },
  mixins: [ Input ],
  props: {
    required: { type: Boolean, default: false },
    label: { type: String, required: true },
    modelValue: { type: Array, default: () => [] },
    participants: { type: Array, required: true },
    anyDuplicateMembershipGroups: { type: Boolean, default: false },
    valid: { type: Boolean, default: true },
  },
  emits: ['update:modelValue', 'update:valid', 'remove-group-split', 'add-group-split'],
  computed: {
    valueIsValid() {
      if (this.currentValue.length < 1) {
        return false
      }
      return this.modelValue.every(split => validSplit(split, this.participants.length))
    },
  },
  watch: {
    valueIsValid: {
      immediate: true,
      handler(isValid) {
        this.$emit('update:valid', isValid)
      },
    },
  },
}
</script>

<style scoped>

</style>
