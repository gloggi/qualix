<template>
  <div>
    <strong>{{ $t('t.views.name_game.well_done') }}</strong>

    <button
      type="submit"
      class="btn btn-outline-primary mr-3 mb-1 w-100 h-25"
      @click="$emit('finish')">
      {{ $t('t.views.name_game.play_again') }}
    </button>
  </div>
</template>

<script>

export default {
  name: 'ScoreScreen',
}
</script>

<style scoped>

</style>
