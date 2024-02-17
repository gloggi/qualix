<template>
  <div class="w-25 w-md-50 w-sm-100 m-auto">
    <div class="d-flex justify-content-between">
      <div class="d-flex">
        <div class="hc fc-30-90-150-255 s e wc">
          <div class="s t wf hf">Zeit:&nbsp;</div>
        </div>
        <div class="hc s e wc">
          <div class="s t wf hf">{{ elapsedTime }}</div>
        </div>
      </div>
      <div class="d-flex">
        <div class="hc fc-30-90-150-255 s e wc">
          <div class="s t wf hf">Punkte:&nbsp;</div>
        </div>
        <div class="hc s e wc">
          <div class="s t wf hf">{{ score }}</div>
        </div>
      </div>
    </div>
    <b-progress :max="participants.length" class="mb-3 mt-1">
      <b-progress-bar :value="step" variant="info">{{ step }} / {{ participants.length }}</b-progress-bar>
    </b-progress>
    <name-game-guess
      v-if="step < shuffledParticipants.length"
      :participant="shuffledParticipants[step]"
      :participants="participants"
      :game-mode="gameMode"
      @correct="correct"
      @incorrect="incorrect"
      @advance="step += 1"
    ></name-game-guess>
    <score-screen v-else :participants="shuffledParticipants" @finish="$emit('finish')"></score-screen>
  </div>
</template>

<script>
import { shuffle } from 'lodash'
import '@formatjs/intl-durationformat/polyfill'
import ButtonSubmit from '../form/ButtonSubmit.vue'
import NameGameGuess from './NameGameGuess.vue'
import ScoreScreen from './ScoreScreen.vue'

export default {
  name: 'NameGameRound',
  components: { ButtonSubmit, NameGameGuess, ScoreScreen },
  props: {
    participants: { type: Array, required: true },
    gameMode: { type: String, required: true }
  },
  data () {
    return {
      shuffledParticipants: shuffle(this.participants),
      step: 0,
      startTime: null,
      elapsedTime: '',
      score: 0,
    };
  },
  computed: {
    finished() {
      return this.step >= this.participants.length
    }
  },
  mounted () {
    this.score = 0
    this.startTime = new Date()
    this.updateTimer()
  },
  methods: {
    updateTimer() {
      const elapsedSeconds = Math.round(((new Date()) - this.startTime) / 100) / 10
      this.elapsedTime = new Intl.DurationFormat('de-CH', { style: 'digital' }).format({
        milliseconds: Math.floor((elapsedSeconds * 1000) % 1000),
        seconds: Math.floor(elapsedSeconds) % 60,
        minutes: Math.floor(elapsedSeconds / 60)
      })
      if (!this.finished) requestAnimationFrame(this.updateTimer)
    },
    correct() {
      this.shuffledParticipants[this.step].correct = true
      this.score += 10
    },
    incorrect() {
      this.shuffledParticipants[this.step].correct = false
    },
  }
};
</script>

<style scoped>

</style>