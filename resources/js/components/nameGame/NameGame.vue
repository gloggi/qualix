<template>
  <div class="name-game">
    <template v-if="!playing">

      <form @submit.prevent="playing = true">

        <input-multi-select
          :label="$t('t.views.name_game.participants')"
          name="selectedParticipants"
          id="participants"
          v-model="selectedParticipantIds"
          multiple
          :options="candidatesWithImage"
          :display-field="anyDuplicateMembershipGroups ? 'name_and_group' : 'scout_name'"
          required
          :groups="groups"
        ></input-multi-select>

        <input-multi-select
          :label="$t('t.views.name_game.game_mode')"
          name="gameMode"
          id="gameMode"
          v-model="gameMode"
          :options="[ { id: 'multipleChoice', label: $t('t.views.name_game.multiple_choice')}, { id: 'manualNameInput', label: $t('t.views.name_game.manual_name_input') } ]"
          required
          :allow-empty="false"
        ></input-multi-select>

        <button-submit :label="tooFewParticipantsSelected ? $t('t.views.name_game.too_few_participants') : $t('t.views.name_game.start')" :disabled="tooFewParticipantsSelected"></button-submit>

      </form>

    </template>
    <template v-else>
      <name-game-round
        :participants="selectedParticipants"
        :game-mode="gameMode"
        @finish="finishRound"
      ></name-game-round>
    </template>
  </div>
</template>

<script>
import InputMultiSelect from '../form/InputMultiSelect'
import ButtonSubmit from '../form/ButtonSubmit'
import { countBy } from 'lodash'

export default {
  name: 'NameGame',
  components: { InputMultiSelect, ButtonSubmit },
  props: {
    participants: { type: Array, required: true },
    teamMembers: { type: Array, default: [] },
  },
  data() {
    return {
      selectedParticipants: this.participants.filter(participant => participant.image_url),
      playing: false,
      gameMode: 'multipleChoice',
    }
  },
  computed: {
    maxParticipantId() {
      return Math.max(...this.participants.map(p => p.id))
    },
    candidates() {
      return this.participants.concat(this.teamMembers.map(teamMember => ({
        ...teamMember,
        id: this.maxParticipantId + teamMember.id,
        scout_name: teamMember.name,
        name_and_group: teamMember.name,
      })))
    },
    candidatesWithImage() {
      return this.candidates.map(candidate => {
        if (candidate.image_url) {
          return { ...candidate, id: '' + candidate.id }
        }
        return {
          ...candidate,
          id: '' + candidate.id,
          scout_name: candidate.scout_name + ' (' + this.$tc('t.views.name_game.no_image') + ')',
        }
      })
    },
    selectedParticipantIds: {
      get () {
        return this.selectedParticipants.map(participant => participant.id).join(',')
      },
      set (newValue) {
        const ids = newValue.split(',')
        this.selectedParticipants = this.candidates.filter(p => ids.includes(p.id.toString()))
      }
    },
    anyDuplicateMembershipGroups() {
      return 1 < Math.max(
        ...Object.values(countBy(
          this.participants.filter(participant => !!participant.group), 'group')
        )
      )
    },
    tooFewParticipantsSelected() {
      return this.selectedParticipants.length < 3
    },
    groups() {
      return {
        [this.$t('t.views.name_game.select_all_participants')]: this.participants.map(p => p.id).join(),
        [this.$t('t.views.name_game.select_all_participants_with_image')]: this.participants.filter(p => p.image_url).map(p => p.id).join(),
        [this.$t('t.views.name_game.select_all_equipe_members')]: this.teamMembers.map(p => this.maxParticipantId + p.id).join(),
        [this.$t('t.views.name_game.select_all')]: this.candidates.map(p => p.id).join(),
      }
    },
  },
  methods: {
    finishRound(selectParticipants = []) {
      this.playing = false
      if (selectParticipants && selectParticipants.length) {
        this.selectedParticipantIds = selectParticipants.join(',')
      }
    }
  }
}
</script>

<style scoped>

</style>
