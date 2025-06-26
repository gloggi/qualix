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
      :name="`${name}[name]`"
      :label="$t('t.views.admin.participant_group_generator.split.name')"
      v-model="currentValue.name"
      required
      narrow-form
    ></input-text>

    <input-text
      :name="`${name}[groups]`"
      v-model="currentValue.groups"
      :label="$t('t.views.admin.participant_group_generator.split.groups')"
      required
      narrow-form>
      <template #append>
        <b-input-group-text>{{ groupSizeText }}</b-input-group-text>
      </template>
    </input-text>

    <row-text narrow-form>
      <b-button variant="link" class="px-0" v-b-toggle="conditionsId">
        {{ $t('t.views.admin.participant_group_generator.split.conditions') }} <i class="fas fa-caret-down"></i>
      </b-button>
    </row-text>

    <b-collapse :id="conditionsId" :visible="false">

      <input-checkbox
        v-if="anyDuplicateMembershipGroups"
        :name="`${name}[forbidMembershipGroups]`"
        :label="$t('t.views.admin.participant_group_generator.split.forbid_membership_groups')"
        v-model="currentValue.forbidMembershipGroups"
        switch
        size="lg"
        narrow-form></input-checkbox>

      <input-multi-multi-select
        :name="`${name}[forbiddenPairings]`"
        v-model="currentValue.forbiddenPairings"
        :label="$t('t.views.admin.participant_group_generator.split.forbidden_pairings')"
        :add-more-label="$t('t.views.admin.participant_group_generator.add_pairing')"
        :options="participants"
        :display-field="anyDuplicateMembershipGroups ? 'name_and_group' : 'scout_name'"
        narrow-form
        :require-multiple="$t('t.views.admin.participant_group_generator.select_multiple_participants')"
        multiple></input-multi-multi-select>

      <input-multi-multi-select
        :name="`${name}[encouragedPairings]`"
        v-model="currentValue.encouragedPairings"
        :label="$t('t.views.admin.participant_group_generator.split.encouraged_pairings')"
        :add-more-label="$t('t.views.admin.participant_group_generator.add_pairing')"
        :options="participants"
        :display-field="anyDuplicateMembershipGroups ? 'name_and_group' : 'scout_name'"
        narrow-form
        :require-multiple="$t('t.views.admin.participant_group_generator.select_multiple_participants')"
        multiple></input-multi-multi-select>

      <input-multi-select
        v-if="unevenSplit"
        :name="`${name}[preferLargeGroup]`"
        v-model="currentValue.preferLargeGroup"
        :label="$t('t.views.admin.participant_group_generator.split.prefer_large_group', { size: largeGroupSize })"
        label-class="col-12"
        :options="participantsWithoutSmallGroupPreference"
        :display-field="anyDuplicateMembershipGroups ? 'name_and_group' : 'scout_name'"
        narrow-form
        multiple></input-multi-select>

      <input-multi-select
        v-if="unevenSplit"
        :name="`${name}[preferSmallGroup]`"
        v-model="currentValue.preferSmallGroup"
        :label="$t('t.views.admin.participant_group_generator.split.prefer_small_group', { size: smallGroupSize })"
        :options="participantsWithoutLargeGroupPreference"
        :display-field="anyDuplicateMembershipGroups ? 'name_and_group' : 'scout_name'"
        narrow-form
        multiple></input-multi-select>
    </b-collapse>
  </b-card>
</template>

<script>
import Input from '../../mixins/input.js'
import InputMultiMultiSelect from '../form/InputMultiMultiSelect.vue'
import { validSplitGroups } from './validSplit.js'
import kebabCase from 'lodash/kebabCase'
import InputText from '../form/InputText.vue'
import RowText from '../form/RowText.vue'
import InputCheckbox from '../form/InputCheckbox.vue'
import InputMultiSelect from '../form/InputMultiSelect.vue';

export default {
  name: 'InputGroupSplit',
  mixins: [ Input ],
  components: { InputMultiSelect, InputCheckbox, RowText, InputText, InputMultiMultiSelect },
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
    largeGroupSize() {
      if (!validSplitGroups(this.currentValue, this.numParticipants)) {
        return 0
      }
      return Math.ceil(this.numParticipants / parseInt(this.currentValue.groups))
    },
    smallGroupSize() {
      if (!validSplitGroups(this.currentValue, this.numParticipants)) {
        return 0
      }
      return Math.floor(this.numParticipants / parseInt(this.currentValue.groups))
    },
    unevenSplit() {
      if (!validSplitGroups(this.currentValue, this.numParticipants)) {
        return false
      }
      return this.largeGroupSize !== this.smallGroupSize
    },
    groupSizeText() {
      if (!validSplitGroups(this.currentValue, this.numParticipants)) {
        return this.$t('t.views.admin.participant_group_generator.split.enter_number_of_groups')
      }
      if (this.smallGroupSize === this.largeGroupSize) {
        return this.$t('t.views.admin.participant_group_generator.split.of_size', { size: this.smallGroupSize })
      }
      return this.$t('t.views.admin.participant_group_generator.split.of_size_between', { min: this.smallGroupSize, max: this.largeGroupSize })
    },
    conditionsId() {
      return kebabCase(`collapse-${this.name}-conditions`)
    },
    participantsWithoutSmallGroupPreference() {
      const preferSmallGroup = this.currentValue.preferSmallGroup.split(',')
      return this.participants.filter(participant => !preferSmallGroup.includes(`${participant.id}`))
    },
    participantsWithoutLargeGroupPreference() {
      const preferLargeGroup = this.currentValue.preferLargeGroup.split(',')
      return this.participants.filter(participant => !preferLargeGroup.includes(`${participant.id}`))
    },
  },
}
</script>

<style scoped>

</style>
