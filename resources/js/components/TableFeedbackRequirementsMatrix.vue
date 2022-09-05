<template>
  <div style="width: 100%; overflow-x: hidden">
    <responsive-table
      fixed
      :data="feedbacksByParticipant"
      :fields="fields"
      :cell-class="cellClass"
      header-class="text-lg-center"
      @clickCell="cellClicked">
      <template #participant="{ row }"><img :src="participantFor(row).image_path" class="avatar-small" :alt="participantFor(row).scout_name"/> <strong>{{ participantFor(row).scout_name }}</strong></template>
      <template #feedbackRequirement="{ row, col }"><requirement-matrix-cell :feedback-requirement="feedbackRequirementFor(row, col)" :requirement-statuses="requirementStatuses"></requirement-matrix-cell>
      </template>
    </responsive-table>
  </div>
</template>

<script>
import ResponsiveTable from "./ResponsiveTable"
import RequirementProgress from './feedback/RequirementProgress'
import { groupBy, sortBy } from 'lodash'

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
  components: {RequirementProgress, ResponsiveTable},
  props: {
    feedbackRequirements: { type: Array, required: true },
    feedbacks: { type: Array, required: true },
    allRequirements: { type: Array, required: true },
    allParticipants: { type: Array, required: true },
    requirementStatuses: { type: Array, default: () => [] },
  },
  computed: {
    feedbacksByParticipant() {
      return sortBy(Object.entries(groupBy(this.feedbacks, 'participant_id')), entry => entry[1][0]?.participant?.scout_name)
    },
    requirements() {
      return groupBy(this.feedbackRequirements, 'requirement_id')
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
          value: row => row[1][0]?.participant?.image_path,
        },
        ...requirementColumns,
      ]
    },
  },
  methods: {
    participantFor(row) {
      return this.allParticipants.find(participant => String(participant.id) === String(row[0]))
    },
    feedbackRequirementFor(row, col) {
      const participantId = String(row[0])
      const requirementId = String(col.requirement.id)
      return this.feedbackRequirements.find(fr => String(fr.feedback.participant_id) === participantId && String(fr.requirement_id) === requirementId)
    },
    requirementStatusFor(row, col) {
      const statusId = this.feedbackRequirementFor(row, col)?.requirement_status_id
      return this.requirementStatuses.find(status => String(status.id) === String(statusId))
    },
    cellClass({ row, col, colIdx }) {
      if (colIdx === 0) return ''

      const color = this.requirementStatusFor(row, col)?.color
      return `bg-${color} text-auto text-${color}-hover bg-auto-hover text-lg-center`
    },
    cellClicked(row, col) {
      const feedbackRequirement = this.feedbackRequirementFor(row, col)
      this.$bvModal.show(`requirement-matrix-cell-${feedbackRequirement.id}`)
    },
    ellipsis,
  },
}
</script>

<style scoped>

</style>
