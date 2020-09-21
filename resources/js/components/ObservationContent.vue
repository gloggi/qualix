<template>
  <p v-if="observation.participants.length > 1" class="multiline mb-0"><a v-for="participant in observation.participants" :key="participant.id" :href="route('participants.detail', { course: participant.course_id, participant: participant.id })" class="badge badge-primary mr-1">{{ participant.scout_name }}</a>
{{ observation.content }}</p>
  <p v-else class="multiline mb-0">{{ observation.content }}</p>
</template>

<script>

export default {
  name: 'ObservationContent',
  props: {
    observation: { type: Object, required: true }
  }
}
</script>

<style scoped>

</style>
