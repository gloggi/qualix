<template>
  <div class="participant-group-generator">
    <input-multi-select
      :label="$t('t.views.admin.participant_group_generator.participants')"
      name=""
      v-model="selectedParticipantIds"
      multiple
      :options="participants"
      display-field="scout_name"
      :groups="{[$t('t.views.admin.participant_group_generator.select_all')]: participants.map(p => p.id).join()}"
      ></input-multi-select>

    <input-multi-select
      v-if="participantGroups.length"
      :label="$t('t.views.admin.participant_group_generator.discourage_existing_participant_groups')"
      name=""
      v-model="selectedParticipantGroupIds"
      multiple
      :options="participantGroups"
      display-field="group_name"
      :groups="{[$t('t.views.admin.participant_group_generator.select_all')]: participantGroups.map(pg => pg.id).join()}"></input-multi-select>

    <input-checkbox
      v-if="anyDuplicateMembershipGroups"
      name=""
      :label="$t('t.views.admin.participant_group_generator.discourage_membership_groups')"
      v-model="discourageMembershipGroups" switch size="lg"></input-checkbox>

    <button-submit :label="$t('t.views.admin.participant_group_generator.generate')" @click.prevent="generate"></button-submit>

    <div v-if="proposedGroups" class="w-100">
      <div v-for="(round, roundIndex) in proposedGroups" class="form-group round-grid mt-3 w-100">
        <div v-for="(proposedGroup, groupIndex) in round.groups" class="group-grid">
          <input class="form-control group-grid-input mt-3 mb-2" type="text" :name="`participantGroups[${roundIndex}-${groupIndex}][group_name]`" v-model="proposedGroup.name" />
          <input-hidden :name="`participantGroups[${roundIndex}-${groupIndex}][participants]`" :value="participantsFormValue(proposedGroup)"></input-hidden>
          <template v-for="participant in proposedGroup.participants">
            <participant-avatar :participant="participant"></participant-avatar>
          </template>
        </div>
      </div>
    </div>

    <button-submit :label="$t('t.global.save')" :disabled="!proposedGroups"></button-submit>
  </div>
</template>

<script>
import { groupBy, countBy } from 'lodash'
import ParticipantAvatar from './ParticipantAvatar'
import InputMultiSelect from '../form/InputMultiSelect'
import InputHidden from '../form/InputHidden'
import RowText from '../form/RowText'
import InputCheckbox from '../form/InputCheckbox'

export default {
  name: 'ParticipantGroupGenerator',
  components: {InputCheckbox, RowText, InputHidden, InputMultiSelect, ParticipantAvatar},
  props: {
    participants: { type: Array, required: true },
    participantGroups: { type: Array, default: () => [] },
  },
  data() {
    return {
      selectedParticipants: this.participants,
      selectedParticipantGroups: this.participantGroups,
      discourageMembershipGroups: '0',
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
    discouragedPairings() {
      return this.discouragedExistingGroups.concat(this.discouragedMembershipGroups)
        .map(discouragedGroup => {
          return discouragedGroup
            .map(participant => this.participantToIndex(participant))
            .filter(index => index !== -1)
        })
        .filter(group => group.length > 1)
    },
    discouragedExistingGroups() {
      return this.selectedParticipantGroups.map(group => group.participants)
    },
    discouragedMembershipGroups() {
      return this.discourageMembershipGroups === '1' ? Object.values(groupBy(this.selectedParticipants, 'group')) : []
    },
  },
  mounted() {
    this.worker.addEventListener('message', this.onResults, false)
  },
  methods: {
    participantToIndex(participant) {
      return this.selectedParticipants.map(p => p.id).indexOf(participant.id)
    },
    indexToParticipant(index) {
      return this.selectedParticipants[index]
    },
    participantsFormValue(group) {
      return group.participants.map(participant => participant.id).join(',')
    },
    generate() {
      this.proposedGroups = null
      this.worker.postMessage({
        numParticipants: this.selectedParticipants.length,
        rounds: [
          { groups: 4, ofSize: 3, forbiddenPairings: [], discouragedPairings: this.discouragedPairings },
          { groups: 3, ofSize: 4, forbiddenPairings: [], discouragedPairings: this.discouragedPairings },
          { groups: 2, ofSize: 7, forbiddenPairings: [], discouragedPairings: this.discouragedPairings },
          { groups: 5, ofSize: 2, forbiddenPairings: [], discouragedPairings: this.discouragedPairings },
          { groups: 3, ofSize: 4, forbiddenPairings: [], discouragedPairings: this.discouragedPairings },
        ],
      })
    },
    onResults(results) {
      console.log(results.data)
      if (!results.data.done) return

      this.proposedGroups = results.data.rounds.map((round, roundIndex) => ({
        name: 'Round ' + (1+roundIndex), // TODO use user-specified round name
        groups: round.map((group, groupIndex) => ({
          name: 'Group ' + (1+roundIndex) + '.' + (1+groupIndex), // TODO use user-specified group name
          participants: group.map(participant => this.indexToParticipant(participant)),
        })),
      }))
    }
  }
}
</script>

<style scoped>

</style>
