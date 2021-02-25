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

import { get } from 'lodash'
import ObservationList from "./ObservationList"
export default {
  name: 'ParticipantDetailObservationList',
  components: {ObservationList},
  props: {
    courseId: { type: String },
    qualisUsingObservations: { type: Object, required: true },
    showRequirements: { type: Boolean, default: false },
    showCategories: { type: Boolean, default: false },
    showImpression: { type: Boolean, default: false },
  },
  computed: {
    actions() {
      return {
        edit: observation => this.routeUri('observation.edit', {course: this.courseId, observation: observation.id}),
        'delete': observation => ({
          text: this.$t('t.views.participant_details.really_delete_observation') + this.qualiMessage(observation),
          route: ['observation.delete', {course: this.courseId, observation: observation.id}]
        })
      }
    }
  },
  methods: {
    qualisUsing(observation) {
      return get(this.qualisUsingObservations, observation.id, [])
    },
    qualiMessage(observation) {
      if (!this.qualisUsing(observation).length) return ''
      return ' ' + this.$tc('t.views.participant_details.qualis_using_observation', this.qualisUsing(observation).length, { qualis: this.qualisUsing(observation).join(', ') })
    }
  },
}
</script>

<style scoped>

</style>
