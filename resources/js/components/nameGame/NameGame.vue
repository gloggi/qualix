<template>
  <div class="name-game">
    <template v-if="!playing">

      <form @submit.prevent="playing = true">

        <input-multi-select
          :label="$t('t.views.name_game.participants')"
          name="selectedParticipants"
          v-model="selectedParticipantIds"
          multiple
          :options="participantsWithImage"
          :display-field="anyDuplicateMembershipGroups ? 'name_and_group' : 'scout_name'"
          required
          :groups="{[$t('t.views.name_game.select_all')]: participants.map(p => p.id).join()}"
        ></input-multi-select>

        <input-multi-select
          :label="$t('t.views.name_game.game_mode')"
          name="gameMode"
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
    participantGroups: { type: Array, default: () => [] },
  },
  data() {
    return {
      selectedParticipants: this.participants.filter(participant => participant.image_url),
      playing: false,
      gameMode: 'multipleChoice',
    }
  },
  computed: {
    participantsWithImage() {
      return this.participants.map(participant => {
        if (participant.image_url) {
          return participant
        }
        return {
          ...participant,
          scout_name: participant.scout_name + ' (' + this.$tc('t.views.name_game.no_image') + ')'
        }
      })
    },
    selectedParticipantIds: {
      get () {
        return this.selectedParticipants.map(participant => participant.id).join(',')
      },
      set (newValue) {
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
    tooFewParticipantsSelected() {
      return this.selectedParticipants.length < 3
    }
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
