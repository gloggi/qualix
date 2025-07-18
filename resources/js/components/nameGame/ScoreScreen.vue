<template>
  <div>
    <strong v-if="wronglyGuessed.length < participants.length">{{ $t('t.views.name_game.well_done') }}</strong>

    <p v-if="wronglyGuessed.length">{{ $t('t.views.name_game.wrong_guesses') }}</p>
    <div v-for="participant in wronglyGuessed" :key="participant.id" class="d-flex flex-row align-items-center gap-1rem mb-2">
      <div class="square-container w-25">
        <img class="card-img-top img img-responsive full-width" :src="participant.image_path" :alt="participant.scout_name">
      </div>
      <div>{{ participant.scout_name }}</div>
    </div>

    <button
      v-if="wronglyGuessed.length && wronglyGuessed.length < participants.length"
      type="submit"
      class="btn btn-outline-primary me-3 mb-1 w-100 h-25"
      @click="$emit('finish', wronglyGuessed.map(p => p.id))">
      {{ $t('t.views.name_game.practice_wrong_names') }}
    </button>
    <button
      type="submit"
      class="btn btn-outline-primary me-3 mb-1 w-100 h-25"
      @click="$emit('finish')">
      {{ $t('t.views.name_game.play_again') }}
    </button>
  </div>
</template>

<script>

export default {
  name: 'ScoreScreen',
  props: {
    participants: { type: Array, default: [] },
  },
  emits: ['finish'],
  computed: {
    wronglyGuessed() {
      return this.participants.filter(participant => participant.correct === false)
    }
  }
}
</script>

<style scoped>

</style>
