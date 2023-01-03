<template>
  <div class="form-group row" :class="{ required }">
    <label :for="name | kebabCase" class="col-form-label" :class="labelClass">{{ label }}</label>

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
      <b-btn
        class="px-0"
        variant="link"
        @click="$emit('add-group-split')"><i class="fas fa-plus mr-1"></i> {{ $t('t.views.admin.participant_group_generator.add_group_split') }}</b-btn>
    </div>
  </div>
</template>

<script>
import Input from '../../mixins/input'
import InputGroupSplit from './InputGroupSplit'
import validSplit from './validSplit'

export default {
  name: 'InputGroupSplits',
  components: { InputGroupSplit },
  mixins: [ Input ],
  props: {
    required: { type: Boolean, default: false },
    label: { type: String, required: true },
    value: { type: Array, default: () => [] },
    participants: { type: Array, required: true },
    anyDuplicateMembershipGroups: { type: Boolean, default: false },
    valid: { type: Boolean, default: true },
  },
  computed: {
    valueIsValid() {
      if (this.currentValue.length < 1) {
        return false
      }
      return this.value.every(split => validSplit(split, this.participants.length))
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
