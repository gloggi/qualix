<template>
  <row-text v-if="image">
    <div class="square-container name-game-image">
      <img class="card-img-top img img-responsive full-width" :src="participant.image_path" :alt="$t('t.views.name_game.who_is_this')">
    </div>
  </row-text>
  <div v-else>{{ participant.scout_name }}</div>
</template>

<script>

export default {
  name: 'GuessPrompt',
  props: {
    participant: { type: Object, required: true },
    gameMode: { type: String, required: true },
  },
  computed: {
    image() {
      return ['multipleChoice', 'manualNameInput'].includes(this.gameMode)
    },
  }
}
</script>

<style scoped>

</style>
