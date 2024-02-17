<template>
  <div>
    <guess-prompt :participant="participant" :game-mode="gameMode"></guess-prompt>
    <form v-if="guessing" ref="form" @submit.prevent="guess">
      <answer-input :participant="participant" :participants="participants" :game-mode="gameMode"></answer-input>
    </form>
    <form v-else @submit.prevent="next">
      <div v-if="correct">
        <div>{{ $t('t.views.name_game.correct') }}</div>
        <div>{{ $t('t.views.name_game.this_is') }}</div>
        <div class="d-flex align-items-baseline justify-content-start">
          <i class="text-green fas fa-check"></i>
          &nbsp;
          {{ participant.scout_name }}
        </div>
      </div>
      <div v-else>
        <div>{{ $t('t.views.name_game.this_is') }}</div>
        <div class="d-flex align-items-baseline justify-content-start">
          {{ participant.scout_name }}
        </div>
        <div>{{ $t('t.views.name_game.you_guessed') }}</div>
        <div class="d-flex align-items-baseline justify-content-start">
          <i class="text-red fas fa-xmark"></i>
          &nbsp;
          <span>{{ submittedScoutName }}</span>
        </div>
      </div>
      <button
        type="submit"
        class="btn btn-outline-primary mr-3 mb-1 w-100 h-25"
        ref="nextButton">
        {{ $t('t.views.name_game.next') }}
      </button>
    </form>
  </div>
</template>

<script>

import ButtonSubmit from '../form/ButtonSubmit.vue'

export default {
  name: 'NameGameGuess',
  components: { ButtonSubmit },
  props: {
    participant: { type: Object, required: true },
    participants: { type: Array, required: true },
    gameMode: { type: String, required: true },
  },
  data() {
    return {
      guessing: true,
      correct: false,
      submittedGuess: null,
      submittedScoutName: '',
    }
  },
  methods: {
    guess(event) {
      this.gameMode === 'multipleChoice' ?
        this.getSelectedParticipant(event) :
        this.getGuessedParticipant(event)
      if (this.submittedGuess?.id === this.participant.id) {
        this.correct = true
        this.$emit('correct')
      } else {
        this.correct = false
        this.$emit('incorrect')
      }
      this.guessing = false
      this.$nextTick(() => this.$refs.nextButton.focus())
    },
    next() {
      this.$emit('advance')
      this.guessing = true
    },
    getSelectedParticipant(event) {
      const selectedId = event.submitter.getAttribute('value')
      this.submittedGuess = this.participants.find(p => selectedId === `${p.id}`)
      this.submittedScoutName = this.submittedGuess?.scout_name
    },
    getGuessedParticipant(event) {
      const input = (new FormData(event.target)).get('scout_name')
      this.submittedGuess = this.participants.find(participant => {
        return this.normalize(participant.scout_name) === this.normalize(input)
      })
      this.submittedScoutName = this.submittedGuess ? this.submittedGuess.scout_name : input
    },
    normalize(name) {
      return name.toLocaleLowerCase().replaceAll(/[^a-zA-Z]/g, '')
    }
  }
}
</script>

<style scoped>

</style>
