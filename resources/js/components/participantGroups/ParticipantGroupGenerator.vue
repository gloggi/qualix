<template>
  <div class="participant-group-generator">
    <form @submit.prevent="generate">
      <b-alert v-if="error" variant="danger" show dismissible fade>
        {{ error }}
      </b-alert>

      <input-group-splits
        name="groupSplits"
        :label="$t('t.views.admin.participant_group_generator.group_splits')"
        :participants="selectedParticipants"
        v-model="groupSplits"
        :valid.sync="groupSplitsValid"
        :any-duplicate-membership-groups="anyDuplicateMembershipGroups"
        required
        @add-group-split="addGroupSplit"
        @remove-group-split="removeGroupSplit">
      </input-group-splits>

      <row-text>
        <help-text id="participant-group-generator-overlap-help" trans="t.views.admin.participant_group_generator.how_to_avoid_overlap"></help-text>
      </row-text>

      <row-text>
        <b-button variant="link" class="px-0" v-b-toggle="'collapse-participant-group-generator-conditions'">
          {{ $t('t.views.admin.participant_group_generator.conditions') }} <i class="fas fa-caret-down"></i>
        </b-button>
      </row-text>

      <b-collapse id="collapse-participant-group-generator-conditions" :visible="false">
        <input-multi-select
          :label="$t('t.views.admin.participant_group_generator.participants')"
          name="participantGroupGeneratorParticipants"
          v-model="selectedParticipantIds"
          multiple
          :options="participants"
          display-field="scout_name"
          required
          :groups="{[$t('t.views.admin.participant_group_generator.select_all')]: participants.map(p => p.id).join()}"
        ></input-multi-select>

        <input-multi-select
          v-if="participantGroups.length"
          :label="$t('t.views.admin.participant_group_generator.discourage_existing_participant_groups')"
          name="participantGroupGeneratorParticipantGroups"
          v-model="selectedParticipantGroupIds"
          multiple
          :options="participantGroups"
          display-field="group_name"
          :groups="{[$t('t.views.admin.participant_group_generator.select_all')]: participantGroups.map(pg => pg.id).join()}"></input-multi-select>

        <input-checkbox
          v-if="anyDuplicateMembershipGroups"
          name="participantGroupGeneratorDiscourageMembershipGroups"
          :label="$t('t.views.admin.participant_group_generator.discourage_membership_groups')"
          v-model="discourageMembershipGroups" switch size="lg"></input-checkbox>

        <input-multi-multi-select
          name="participantGroupGeneratorDiscouragedPairings"
          v-model="discouragedPairings"
          :label="$t('t.views.admin.participant_group_generator.discouraged_pairings')"
          :add-more-label="$t('t.views.admin.participant_group_generator.add_pairing')"
          :options="selectedParticipants"
          display-field="scout_name"
          :require-multiple="$t('t.views.admin.participant_group_generator.select_multiple_participants')"></input-multi-multi-select>
      </b-collapse>

      <button-submit
        :label="$t('t.views.admin.participant_group_generator.generate')"
        :disabled="!groupSplitsValid || selectedParticipants.length < 3 || inProgress">
        <b-progress v-if="inProgress" :max="100" animated class="mb-3 mt-1">
          <b-progress-bar :value="progress">{{ progress }}%</b-progress-bar>
        </b-progress>
      </button-submit>
    </form>

    <input-generated-participant-groups
      name="participantGroups"
      v-model="proposedGroups"
      :participants="participants"></input-generated-participant-groups>

    <button-submit :label="$t('t.global.save')" :disabled="!proposedGroups"></button-submit>
  </div>
</template>

<script>
import { groupBy, countBy } from 'lodash'
import ParticipantAvatar from './ParticipantAvatar'
import InputMultiSelect from '../form/InputMultiSelect'
import InputMultiMultiSelect from '../form/InputMultiMultiSelect'
import InputHidden from '../form/InputHidden'
import RowText from '../form/RowText'
import ButtonSubmit from '../form/ButtonSubmit'
import InputCheckbox from '../form/InputCheckbox'
import InputGroupSplits from './InputGroupSplits'
import HelpText from '../HelpText'
import InputGeneratedParticipantGroups from './InputGeneratedParticipantGroups'

export default {
  name: 'ParticipantGroupGenerator',
  components: {
    InputGeneratedParticipantGroups,
    InputCheckbox, RowText, InputHidden, InputMultiSelect, ParticipantAvatar, InputGroupSplits, HelpText, InputMultiMultiSelect, ButtonSubmit},
  props: {
    participants: { type: Array, required: true },
    participantGroups: { type: Array, default: () => [] },
  },
  data() {
    return {
      selectedParticipants: this.participants,
      selectedParticipantGroups: this.participantGroups,
      discourageMembershipGroups: '0',
      discouragedPairings: [''],
      groupSplits: [this.defaultGroupSplit()],
      groupSplitsValid: true,
      inProgress: false,
      progress: 0,
      error: null,
      worker: new Worker(new URL('./index.worker.js', import.meta.url)),
      proposedGroups: null,
    }
  },
  computed: {
    selectedParticipantIds: {
      get() {
        return this.selectedParticipants.map(participant => participant.id).join(',')
      },
      set(newValue) {
        const ids = newValue.split(',')
        this.selectedParticipants = this.participants.filter(p => ids.includes(p.id.toString()))
      }
    },
    anyDuplicateMembershipGroups() {
      return 1 < Math.max(
        ...Object.values(countBy(
          this.selectedParticipants.filter(participant => !!participant.group), 'group')
        )
      )
    },
    selectedParticipantGroupIds: {
      get() {
        return this.selectedParticipantGroups.map(participantGroup => participantGroup.id).join(',')
      },
      set(newValue) {
        const ids = newValue.split(',')
        this.selectedParticipantGroups = this.participantGroups.filter(pg => ids.includes(pg.id.toString()))
      }
    },
    discouragedExistingGroups() {
      return this.selectedParticipantGroups.map(group => group.participants)
    },
    membershipGroupPairings() {
      return Object.values(groupBy(this.selectedParticipants, 'group'))
    },
  },
  mounted() {
    this.worker.addEventListener('message', this.onResults, false)
    if (Object.keys(window.Laravel.errors).length) {
      this.error = this.$t('t.views.admin.participant_group_generator.validation_errors')
    }
  },
  methods: {
    participantToIndex(participant) {
      return this.selectedParticipants.map(p => p.id)
        .indexOf(typeof participant === 'string' ? parseInt(participant) : participant.id)
    },
    indexToParticipant(index) {
      return this.selectedParticipants[index]
    },
    defaultGroupSplit() {
      return {
        id: (Math.random() + 1).toString(36).substring(2,7),
        name: this.$t('t.views.admin.participant_group_generator.default_split_name'),
        groups: String(Math.ceil(this.participants.length / Math.max(1, Math.min(this.participants.length, 4)))),
        forbidMembershipGroups: '0',
        forbiddenPairings: [''],
        encouragedPairings: [''],
      }
    },
    addGroupSplit() {
      this.groupSplits = [...this.groupSplits, this.defaultGroupSplit()]
    },
    removeGroupSplit(id) {
      this.groupSplits = this.groupSplits.filter(split => split.id !== id)
    },
    preparePairings(pairings) {
      return pairings
        .map(group => group.map(participant => this.participantToIndex(participant)).filter(index => index !== -1))
        .filter(group => group.length > 1)
    },
    generate() {
      this.progress = 0
      this.inProgress = true
      this.proposedGroups = []
      window.Laravel.errors = {}
      window.Laravel.oldInput.participantGroups = {}
      this.worker.postMessage({
        numParticipants: this.selectedParticipants.length,
        rounds: this.groupSplits.map(split => ({
          ...split,
          ofSize: Math.ceil(this.selectedParticipants.length / parseInt(split.groups)),
          discouragedPairings: this.preparePairings([
            ...this.discouragedPairings.map(pairing => pairing.split(',')),
            ...this.discouragedExistingGroups,
            ...(this.discourageMembershipGroups === '1' ? this.membershipGroupPairings : [])
          ]),
          forbiddenPairings: this.preparePairings([
            ...split.forbiddenPairings.map(pairing => pairing.split(',')),
            ...(split.forbidMembershipGroups === '1' ? this.membershipGroupPairings : []),
          ]),
          encouragedPairings: this.preparePairings([
            ...split.encouragedPairings.map(pairing => pairing.split(',')),
          ])
        })),
      })
    },
    onResults(results) {
      this.progress = results.data.progress
      if (!results.data.done) return

      this.inProgress = false
      this.progress = 0
      this.proposedGroups = results.data.rounds.map((round, roundIndex) => (
        round.map((group, groupIndex) => ({
          group_name: this.groupSplits[roundIndex].name + ' ' + (1 + groupIndex),
          participants: group.map(participant => this.indexToParticipant(participant).id).join(','),
        }))
      ))
    }
  }
}
</script>

<style scoped>

</style>
