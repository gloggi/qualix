<template>
  <b-table-simple
    hover
    fixed
    class="table-responsive-cards"
    v-bind="$attrs">
    <b-thead>
      <requirements-matrix-header-row :requirements="sortedRequirements" />
    </b-thead>
    <b-tbody>
      <requirements-matrix-row
        v-for="feedback in sortedFeedbacks"
        :key="feedback.id"
        :feedback="feedback"
        :all-requirements="allRequirements"
        :requirement-statuses="requirementStatuses"
        :evaluation-grids="evaluationGrids.filter(grid => grid.participants.map(p => p.id).includes(feedback.participant.id))"
        :collaboration-enabled="collaborationEnabled"
      />
    </b-tbody>
  </b-table-simple>
</template>

<script>
import {sortBy, uniqBy} from 'lodash'
import RequirementsMatrixHeaderRow from './RequirementsMatrixHeaderRow'
import RequirementsMatrixRow from './RequirementsMatrixRow'

export default {
  name: 'RequirementsMatrix',
  components: {RequirementsMatrixHeaderRow, RequirementsMatrixRow},
  props: {
    feedbackRequirements: { type: Array, required: true },
    feedbacks: { type: Array, required: true },
    allRequirements: { type: Array, required: true },
    requirementStatuses: { type: Array, default: () => [] },
    evaluationGrids: {type: Array, default: () => []},
    collaborationEnabled: { type: Boolean, default: false },
  },
  computed: {
    sortedRequirements() {
      return sortBy(uniqBy(this.feedbackRequirements, 'requirement_id'), 'requirement_id')
        .map(fr => fr.requirement)
    },
    sortedFeedbacks() {
      return sortBy(this.feedbacks, feedback => feedback.participant?.scout_name)
    },
  }
}
</script>
