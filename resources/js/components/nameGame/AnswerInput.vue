<template>
  <div v-if="gameMode === 'multipleChoice'">
    <button
      v-for="option in multipleChoiceOptions"
      :key="option.id"
      type="submit"
      class="btn btn-primary mr-3 mb-1 w-100 h-25"
      :value="option.id">
      {{ option.scout_name }}
    </button>
  </div>
  <div v-else>
    <input-text v-model="manualInput" :label="$t('t.views.name_game.scout_name')" name="scout_name" ref="scoutName"></input-text>
    <button
      type="submit"
      class="btn btn-primary mr-3 mb-1 w-100 h-25">
      {{ buttonLabel }}
    </button>
  </div>
</template>

<script>

import InputText from '../form/InputText.vue';

export default {
  name: 'AnswerInput',
  components: { InputText },
  props: {
    participant: { type: Object, required: true },
    participants: { type: Array, required: true },
    gameMode: { type: String, required: true },
  },
  data: () => ({
    manualInput: '',
  }),
  computed: {
    wrongGuess1() {
      let result = this.participant
      if (this.participants.length < 2) return result
      while (result.id === this.participant.id) {
        result = this.participants[Math.floor(Math.random() * this.participants.length)]
      }
      return result
    },
    wrongGuess2() {
      let result = this.participant
      if (this.participants.length < 3) return result
      while (result.id === this.participant.id || result.id === this.wrongGuess1.id) {
        result = this.participants[Math.floor(Math.random() * this.participants.length)]
      }
      return result
    },
    multipleChoiceOptions() {
      const options = [this.participant, this.wrongGuess1, this.wrongGuess2]
      options.sort((a, b) => a.scout_name.localeCompare(b.scout_name))
      return options
    },
    buttonLabel() {
      if (this.manualInput.length) {
        return this.$t('t.views.name_game.submit')
      }
      return this.$t('t.views.name_game.skip')
    },
  },
  mounted() {
    if (this.gameMode === 'manualNameInput' && this.$refs.scoutName) {
      this.manualInput = ''
      this.$refs.scoutName.focus()
    }
  },
}
</script>

<style scoped>

</style>
