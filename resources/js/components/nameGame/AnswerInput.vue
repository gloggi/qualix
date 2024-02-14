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
  <div v-else>{{ participant.scout_name }}</div>
</template>

<script>

export default {
  name: 'AnswerInput',
  props: {
    participant: { type: Object, required: true },
    participants: { type: Array, required: true },
    gameMode: { type: String, required: true },
  },
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
    }
  }
}
</script>

<style scoped>

</style>
