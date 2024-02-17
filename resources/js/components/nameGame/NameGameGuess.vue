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
          <span>{{ submittedGuess.scout_name }}</span>
        </div>
      </div>
      <button
        type="submit"
        class="btn btn-outline-primary mr-3 mb-1 w-100 h-25">
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
    }
  },
  methods: {
    guess(event) {
      const selectedId = event.submitter.getAttribute('value')
      this.submittedGuess = this.participants.find(p => selectedId === `${p.id}`)
      if (this.submittedGuess.id === this.participant.id) {
        this.correct = true
        this.$emit('correct')
      } else {
        this.correct = false
        this.$emit('incorrect')
      }
      this.guessing = false
    },
    next() {
      this.$emit('advance')
      this.guessing = true
    }
  }
}
</script>

<style scoped>

</style>
