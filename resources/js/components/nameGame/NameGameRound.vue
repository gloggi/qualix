<template>
  <div class="w-100 w-md-50 m-auto">
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
    <b-progress class="position-relative mb-3 mt-1" :max="participants.length" >
      <span class="justify-content-center align-self-center d-flex position-absolute w-100"><strong class="ml-1"> {{ $t('t.views.name_game.num_correct_and_incorrect', { correct: numCorrect, incorrect: numIncorrect, total: participants.length }) }}</strong></span>
      <b-progress-bar :value="numCorrect" variant="info"></b-progress-bar>
      <b-progress-bar :value="numIncorrect" variant="danger"></b-progress-bar>
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
    <score-screen v-else :participants="shuffledParticipants" @finish="(...args) => this.$emit('finish', ...args)"></score-screen>
  </div>
</template>

<script>
import { cloneDeep, shuffle } from 'lodash';
import '@formatjs/intl-durationformat/polyfill'
import ButtonSubmit from '../form/ButtonSubmit.vue'
import NameGameGuess from './NameGameGuess.vue'
import ScoreScreen from './ScoreScreen.vue'
import Vue from 'vue';

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
    },
    numCorrect() {
      return this.shuffledParticipants.filter(participant => participant.correct === true).length
    },
    numIncorrect() {
      return this.shuffledParticipants.filter(participant => participant.correct === false).length
    },
  },
  mounted () {
    this.start()
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
      Vue.set(this.shuffledParticipants[this.step], 'correct', true)
      this.score += 10
    },
    incorrect() {
      Vue.set(this.shuffledParticipants[this.step], 'correct', false)
    },
    start() {
      this.shuffledParticipants = shuffle(this.participants.map(cloneDeep))
      this.step = 0
      this.startTime = new Date()
      this.elapsedTime = ''
      this.score = 0
    },
  },
};
</script>

<style scoped>

</style>
