<template>
  <div style="width: 100%; overflow-x: hidden">
    <responsive-table
      fixed
      :data="feedbacksByParticipant"
      :fields="fields"
      :cell-class="cellClass"
      header-class="text-lg-center"
      @clickCell="cellClicked">
      <template #participant="{ row }"><requirements-matrix-row
        :feedback-requirements="feedbackRequirementsFor(row)"
        :feedback="feedbackFor(row)"
        @remoteinput="(fr) => updateFeedbackRequirements(fr, row)"
      /></template>
      <template #feedbackRequirement="{ row, col }"><a href="javascript:void(0)"><requirement-matrix-cell
        :feedback-requirement="feedbackRequirementFor(row, col)"
        :requirement-statuses="requirementStatuses"
        @input="(update) => updateFeedbackRequirement(update, feedbackRequirementFor(row, col))"
      /></a></template>
    </responsive-table>
  </div>
</template>

<script>
import ResponsiveTable from "./ResponsiveTable"
import {groupBy, sortBy} from 'lodash'
import RequirementsMatrixRow from './RequirementsMatrixRow'

const ellipsis = function(text, max) {
  if (text.length <= max) {
    return text;
  }
  const dots = '…';
  let i = dots.length;
  text = text.split(' ').filter(function (word) {
    i += word.length;
    if (i > max) {
      return false;
    }
    i += 1; // add a space character after a word
    return true;
  }).join(' ').replace(/(,|\n|\r\n|\.|\?|!)+$/, '');

  return text + dots;
}

export default {
  name: 'TableFeedbackRequirementsMatrix',
  components: {RequirementsMatrixRow, ResponsiveTable},
  props: {
    feedbackRequirements: { type: Array, required: true },
    feedbacks: { type: Array, required: true },
    allRequirements: { type: Array, required: true },
    allParticipants: { type: Array, required: true },
    requirementStatuses: { type: Array, default: () => [] },
  },
  data: function() {
    return {
      currentFeedbackRequirements: this.feedbackRequirements,
    }
  },
  computed: {
    feedbacksByParticipant() {
      return sortBy(Object.entries(groupBy(this.feedbacks, 'participant_id')), entry => entry[1][0]?.participant?.scout_name)
    },
    requirements() {
      return groupBy(this.currentFeedbackRequirements, 'requirement_id')
    },
    fields() {
      const requirementColumns = Object.keys(this.requirements).map(requirementId => {
        const requirement = this.allRequirements.find(requirement => String(requirement.id) === requirementId)
        return {
          label: requirement?.content,
          slot: 'feedbackRequirement',
          requirement: requirement
        }
      })
      return [
        {
          label: '',
          slot: 'participant',
        },
        ...requirementColumns,
      ]
    },
  },
  methods: {
    feedbackFor(row) {
      return this.feedbacks.find(feedback => String(feedback.participant_id) === String(row[0]))
    },
    feedbackRequirementsFor(row) {
      const participantId = String(row[0])
      return this.currentFeedbackRequirements.filter(fr => String(fr.feedback.participant_id) === participantId)
    },
    feedbackRequirementFor(row, col) {
      const requirementId = String(col.requirement.id)
      return this.feedbackRequirementsFor(row).find(fr => String(fr.requirement_id) === requirementId)
    },
    requirementStatusFor(row, col) {
      const statusId = this.feedbackRequirementFor(row, col)?.requirement_status_id
      return this.requirementStatuses.find(status => String(status.id) === String(statusId))
    },
    cellClass({ row, col, colIdx }) {
      if (colIdx === 0) return ''

      const color = this.requirementStatusFor(row, col)?.color
      return `requirements-matrix-cell bg-${color} text-auto text-${color}-hover bg-auto-hover text-lg-center cursor-pointer`
    },
    cellClicked(row, col) {
      const feedbackRequirement = this.feedbackRequirementFor(row, col)
      this.$bvModal.show(`requirement-matrix-cell-${feedbackRequirement.id}`)
    },
    updateFeedbackRequirements(feedbackRequirements, row) {
      feedbackRequirements.forEach(updatedFeedbackRequirement => {
        const feedbackRequirement = this.feedbackRequirementFor(row, { requirement: updatedFeedbackRequirement })
        this.updateFeedbackRequirement(updatedFeedbackRequirement, feedbackRequirement)
      })
    },
    updateFeedbackRequirement(newData, feedbackRequirement) {
      feedbackRequirement.requirement_status_id = newData.status_id
    },
    ellipsis,
  },
}
</script>
