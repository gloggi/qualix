<template>
  <div class="form-group row" :class="{ required }">
    <label :for="name | kebabCase" class="col-md-3 col-form-label text-md-right">{{ label }}</label>

    <div class="col-md-6">
      <template v-for="split in value">
        <input-group-split
          :name="'group-split-' + split.split.id"
          :num-participants="numParticipants"
          :any-duplicate-membership-groups="anyDuplicateMembershipGroups"
          v-model="split.split"
          :deletable="value.length > 1"
          @remove="$emit('remove-group-split', split.split.id)"></input-group-split>
      </template>
      <b-btn
        class="px-0"
        variant="link"
        @click="$emit('add-group-split')">{{ $t('t.views.admin.participant_group_generator.add_group_split') }}</b-btn>
    </div>
  </div>
</template>

<script>
import Input from '../../mixins/input'
import InputGroupSplit from './InputGroupSplit'
import validSplit from './validGroupSplit'

export default {
  name: 'InputGroupSplits',
  components: { InputGroupSplit },
  mixins: [ Input ],
  props: {
    required: { type: Boolean, default: false },
    label: { type: String, required: true },
    value: { type: Array, default: () => [] },
    numParticipants: { type: Number, required: true },
    anyDuplicateMembershipGroups: { type: Boolean, default: false },
    valid: { type: Boolean, default: true },
  },
  computed: {
    valueIsValid() {
      if (this.value.length < 1) {
        return false
      }
      return this.value.some(({ split }) => validSplit(split, this.numParticipants))
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
  methods: {

  }
}
</script>

<style scoped>

</style>
