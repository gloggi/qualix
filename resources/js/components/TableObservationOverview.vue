<template>
  <responsive-table
    :data="participants"
    :actions="actions"
    :fields="fields"
    :header-class="headerClass"
    :cell-class="cellClass">
    <template v-if="anyUserImages" v-for="user in users" v-slot:[headerSlotName(user)]="{ col: { user } }">
      <div class="d-flex flex-column align-items-center"><img :src="user.image_path" class="avatar-small" :alt="user.name"/>{{ user.name }}</div>
    </template>
    <template #feedback="{ row }">
      <a v-if="feedbackFor(row)"
         :href="routeUri('feedbackContent.edit', {course: feedbackData.course_id, participant: row.id, feedback: feedbackFor(row).id})"
         target="_blank" class="text-decoration-none">
        <requirement-progress :requirements="feedbackFor(row).requirements" :statuses="requirementStatuses"></requirement-progress>
      </a>
    </template>
  </responsive-table>
</template>

<script>
import ResponsiveTable from "./ResponsiveTable"
import RequirementProgress from './feedback/RequirementProgress'

export default {
  name: 'TableObservationOverview',
  components: {RequirementProgress, ResponsiveTable},
  props: {
    users: { type: Array, required: true },
    participants: { type: Array, required: true },
    feedbackData: { type: Object, default: null },
    requirementStatuses: { type: Array, default: () => [] },
    multiple: { type: Boolean, default: false },
    redThreshold: { type: Number, default: 5 },
    greenThreshold: { type: Number, default: 10 },
  },
  computed: {
    actions() {
      if (!this.multiple) return {}
      return {
        binoculars: participant => this.routeUri('observation.new', {
          course: participant.course_id,
          participant: participant.id
        })
      }
    },
    fields() {
      const totalColumn = [{ label: this.$t('t.global.total'), value: participant => this.totalObservations(participant) }]
      const observationColumns = this.users.map(user => ({ label: user.name, headerSlot: this.headerSlotName(user), user: user, value: participant => participant.observation_counts_by_user[user.id] || 0}))
      if (!this.multiple) {
        return totalColumn.concat(observationColumns)
      }
      const feedbackColumn = this.feedbackData ? [{
        label: this.feedbackData.name,
        slot: 'feedback',
      }] : []
      return [
        {
          label: this.$t('t.models.observation.participants'),
          value: participant => participant.scout_name,
          href: participant => this.routeUri('participants.detail', { course: participant.course_id, participant: participant.id })
        },
        ...totalColumn,
        ...observationColumns,
        ...feedbackColumn,
      ]
    },
    anyUserImages() {
      return this.users.some(user => user.image_url)
    }
  },
  methods: {
    totalObservations(participant) {
      return this.users.reduce((sum, user) => sum + (participant.observation_counts_by_user[user.id] || 0), 0)
    },
    headerClass({ col, colIdx }) {
      if (colIdx === (this.multiple ? 1 : 0)) return ''
      return 'text-lg-center'
    },
    cellClass({ cellValue, colIdx }) {
      if (colIdx === (this.multiple ? 1 : 0)) return ''
      if (cellValue < this.redThreshold) return 'text-lg-center bg-danger-light'
      if (cellValue >= this.greenThreshold) return 'text-lg-center bg-success-light'
      return 'text-lg-center'
    },
    feedbackFor({ id }) {
      return this.feedbackData?.feedbacks?.find(feedback => feedback.participant_id === id)
    },
    headerSlotName(user) {
      return 'user-' + user.id
    },
  },
}
</script>

<style scoped>

</style>
