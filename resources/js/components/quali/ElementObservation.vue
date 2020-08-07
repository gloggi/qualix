<template>
  <div class="card">
    <div class="card-header d-flex pr-2">
      <div class="flex-grow-1">{{ translations['t.models.observation.model_name'] }}</div>
      <div><i class="fas fa-arrows-alt mr-2 handle"></i></div>
    </div>
    <div class="card-body bg-light">
      <p class="multiline mb-0">{{ value.content }}</p>
      <p class="card-text"><small class="text-muted">{{ value.block }}, {{ value.date }}</small></p>
    </div>
  </div>
</template>

<script>

export default {
  name: 'ElementObservation',
  props: {
    value: Object,
    translations: Object,
  }
}
</script>

<style scoped>

</style>
