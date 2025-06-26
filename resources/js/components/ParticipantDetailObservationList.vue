<template>
  <observation-list
    v-bind="$attrs"
    :course-id="courseId"
    :actions="actions"
    :show-requirements="showRequirements"
    :show-categories="showCategories"
    :show-impression="showImpression"></observation-list>
</template>

<script>
import get from 'lodash/get'
import ObservationList from './ObservationList.vue'

export default {
  name: 'ParticipantDetailObservationList',
  components: {ObservationList},
  props: {
    courseId: { type: String },
    feedbacksUsingObservations: { type: Object, required: true },
    showRequirements: { type: Boolean, default: false },
    showCategories: { type: Boolean, default: false },
    showImpression: { type: Boolean, default: false },
  },
  computed: {
    actions() {
      return {
        edit: observation => this.routeUri('observation.edit', {course: this.courseId, observation: observation.id}),
        'delete': observation => ({
          text: this.$t('t.views.participant_details.really_delete_observation') + this.feedbackMessage(observation),
          route: ['observation.delete', {course: this.courseId, observation: observation.id}]
        })
      }
    }
  },
  methods: {
    feedbacksUsing(observation) {
      return Object.values(get(this.feedbacksUsingObservations, observation.id, {}))
    },
    feedbackMessage(observation) {
      if (!this.feedbacksUsing(observation).length) return ''
      return ' ' + this.$tc('t.views.participant_details.feedbacks_using_observation', this.feedbacksUsing(observation).length, { feedbacks: this.feedbacksUsing(observation).join(', ') })
    }
  },
}
</script>

<style scoped>

</style>
