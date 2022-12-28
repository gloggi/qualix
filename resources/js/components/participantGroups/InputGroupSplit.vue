<template>
  <b-card bg-variant="light">
    <b-button
      variant="link"
      class="card-remove-button text-danger"
      @click="$emit('remove')"
      :title="$t('t.global.remove')"
      :aria-label="$t('t.global.remove')"
      :disabled="!deletable">
      <i class="fas fa-circle-minus"></i>
    </b-button>
    <input-text
      :name="`group-split-${value.id}-name`"
      :label="$t('t.views.admin.participant_group_generator.split.name')"
      v-model="value.name"
      required
      narrow-form
    ></input-text>

    <input-text
      :name="`group-split-${value.id}-groups`"
      v-model="value.groups"
      :label="$t('t.views.admin.participant_group_generator.split.groups')"
      required
      narrow-form>
      <template #append>
        <b-input-group-text>{{ groupSizeText }}</b-input-group-text>
      </template>
    </input-text>

    <row-text narrow-form>
      <b-button variant="link" class="px-0" v-b-toggle="`group-split-${value.id}-conditions`">
        {{ $t('t.views.admin.participant_group_generator.split.conditions') }} <i class="fas fa-caret-down"></i>
      </b-button>
    </row-text>

    <b-collapse :id="`group-split-${value.id}-conditions`" :visible="false">

      <input-checkbox
        v-if="anyDuplicateMembershipGroups"
        :name="`group-split-${value.id}-forbid-membership-groups`"
        :label="$t('t.views.admin.participant_group_generator.split.forbid_membership_groups')"
        v-model="value.forbidMembershipGroups"
        switch
        size="lg"
        narrow-form></input-checkbox>

      <input-multi-multi-select
        :name="`group-split-${value.id}-forbidden-pairings`"
        v-model="value.forbiddenPairings"
        :label="$t('t.views.admin.participant_group_generator.split.forbidden_pairings')"
        :add-more-label="$t('t.views.admin.participant_group_generator.split.add_pairing')"
        :options="participants"
        display-field="scout_name"
        narrow-form
        :require-multiple="$t('t.views.admin.participant_group_generator.split.select_multiple_participants')"
        multiple></input-multi-multi-select>

      <input-multi-multi-select
        :name="`group-split-${value.id}-encouraged-pairings`"
        v-model="value.encouragedPairings"
        :label="$t('t.views.admin.participant_group_generator.split.encouraged_pairings')"
        :add-more-label="$t('t.views.admin.participant_group_generator.split.add_pairing')"
        :options="participants"
        display-field="scout_name"
        narrow-form
        :require-multiple="$t('t.views.admin.participant_group_generator.split.select_multiple_participants')"
        multiple></input-multi-multi-select>
    </b-collapse>
  </b-card>
</template>

<script>
import Input from '../../mixins/input'
import InputMultiMultiSelect from '../form/InputMultiMultiSelect'
import validGroupSplit from './validGroupSplit'

export default {
  name: 'InputGroupSplit',
  mixins: [ Input ],
  components: { InputMultiMultiSelect },
  props: {
    value: { type: Object, default: () => ({}) },
    participants: { type: Array, required: true },
    anyDuplicateMembershipGroups: { type: Boolean, default: false },
    deletable: { type: Boolean, default: true },
  },
  computed: {
    numParticipants() {
      return this.participants.length
    },
    groupSizeText() {
      if (!validGroupSplit(this.value, this.numParticipants)) {
        return this.$t('t.views.admin.participant_group_generator.split.enter_number_of_groups')
      }
      const groups = parseInt(this.value.groups)
      const min = Math.floor(this.numParticipants / groups)
      const max = Math.ceil(this.numParticipants / groups)
      if (min === max) {
        return this.$t('t.views.admin.participant_group_generator.split.of_size', { size: min })
      }
      return this.$t('t.views.admin.participant_group_generator.split.of_size_between', { min, max })
    },
  },
}
</script>

<style scoped>

</style>
