<template>
  <div :class="{ 'advanced-matrix-rendering': isAdvancedRenderingActive }">
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
          @mouseenter="trackHover(feedback.id)"
        />
      </b-tbody>
    </b-table-simple>

    <requirements-matrix-optimizer
      v-if="isAdvancedRenderingActive && !isAnalyzerActive"
      @deactivate="isAdvancedRenderingActive = false"
      @analyze="isAnalyzerActive = true"
    />

    <requirements-matrix-analyzer
      v-if="isAnalyzerActive"
      @exit="isAnalyzerActive = false; isAdvancedRenderingActive = false"
    />
  </div>
</template>

<script>
import sortBy from 'lodash/sortBy'
import uniqBy from 'lodash/uniqBy'
import RequirementsMatrixHeaderRow from './RequirementsMatrixHeaderRow.vue'
import RequirementsMatrixRow from './RequirementsMatrixRow.vue'

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
  data() {
    return {
      isAdvancedRenderingActive: false,
      isAnalyzerActive: false,
      hoverTracking: [],
    }
  },
  computed: {
    sortedRequirements() {
      return sortBy(uniqBy(this.feedbackRequirements, 'requirement_id'), 'requirement_id')
        .map(fr => fr.requirement)
    },
    sortedFeedbacks() {
      return sortBy(this.feedbacks, feedback => feedback.participant?.scout_name)
    },
  },
  methods: {
    trackHover(id) {
      const now = Date.now();
      this.hoverTracking.push({id, time: now});
      this.hoverTracking = this.hoverTracking.filter(h => now - h.time < 500);

      const uniqueIds = new Set(this.hoverTracking.map(h => h.id));
      if (uniqueIds.size >= 8 && this.hoverTracking.length >= 20) {
        this.hoverTracking = [];
        this.isAdvancedRenderingActive = true;
      }
    }
  }
}
</script>

<style>
.advanced-matrix-rendering {
  background-color: #1a1a1a !important;
  color: #fff;
  transition: background-color 1s ease;
  min-height: 80vh;
  border-radius: 8px;
  padding: 10px;
}

.advanced-matrix-rendering table {
  color: #fca311;
  text-shadow: 0 0 5px rgba(255, 140, 0, 0.6);
}

.advanced-matrix-rendering th,
.advanced-matrix-rendering td {
  border-color: #333 !important;
}
.advanced-matrix-rendering .table-hover tbody tr:hover {
  color: #fff;
  background-color: rgba(255, 140, 0, 0.1);
}
</style>
