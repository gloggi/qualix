<template>
  <div class="name-game">
    <template v-if="!playing">

      <form @submit.prevent="playing = true">

        <input-multi-select
          :label="$t('t.views.name_game.participants')"
          name="selectedParticipants"
          v-model="selectedParticipantIds"
          multiple
          :options="participants"
          :display-field="anyDuplicateMembershipGroups ? 'name_and_group' : 'scout_name'"
          required
          :groups="{[$t('t.views.name_game.select_all')]: participants.map(p => p.id).join()}"
        ></input-multi-select>

        <button-submit :label="$t('t.views.name_game.start')"></button-submit>

      </form>

    </template>
    <template v-else>
      <name-game-round
        :participants="selectedParticipants"
        :game-mode="gameMode"
        @finish="playing = false"
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
      selectedParticipants: this.participants,
      playing: false,
      gameMode: 'multipleChoice',
    }
  },
  computed: {
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
  }
}
</script>

<style scoped>

</style>
