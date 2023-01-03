<template>
  <div class="text-center" v-b-tooltip.hover :title="participant.scout_name">
    <div class="mb-0 position-relative">
      <img class="card-img-top rounded-circle img img-responsive full-width" :src="participant.image_path" :alt="participant.scout_name">
      <p>{{ showGroup ? participant.name_and_group : participant.scout_name }}</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ParticipantAvatar',
  props: {
    participant: { type: Object, required: true },
    showGroup: { type: Boolean, default: false },
  },
}
</script>

<style scoped>

</style>
