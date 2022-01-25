<template>
  <responsive-table
    :data="participants"
    :actions="actions"
    :fields="fields"
    :cell-class="cellClass">
    <template #quali="{ row }">
      <a v-if="qualiFor(row)"
         :href="routeUri('qualiContent.edit', {course: qualiData.course_id, participant: row.id, quali: qualiFor(row).id})"
         target="_blank" class="text-decoration-none">
        <requirement-progress :requirements="qualiFor(row).requirements"></requirement-progress>
      </a>
    </template>
  </responsive-table>
</template>

<script>

import ResponsiveTable from "./ResponsiveTable"
import RequirementProgress from './quali/RequirementProgress'
export default {
  name: 'TableObservationOverview',
  components: {RequirementProgress, ResponsiveTable},
  props: {
    users: { type: Array, required: true },
    participants: { type: Array, required: true },
    qualiData: { type: Object, default: null },
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
      const observationColumns = this.users.map(user => ({ label: user.name, value: participant => participant.observation_counts_by_user[user.id] || 0}))
      if (!this.multiple) {
        return totalColumn.concat(observationColumns)
      }
      const qualiColumn = this.qualiData ? [{
        label: this.qualiData.name,
        slot: 'quali',
      }] : []
      return [
        {
          label: this.$t('t.models.observation.participants'),
          value: participant => participant.scout_name,
          href: participant => this.routeUri('participants.detail', { course: participant.course_id, participant: participant.id })
        },
        ...totalColumn,
        ...observationColumns,
        ...qualiColumn,
      ]
    }
  },
  methods: {
    totalObservations(participant) {
      return this.users.reduce((sum, user) => sum + (participant.observation_counts_by_user[user.id] || 0), 0)
    },
    cellClass({ cellValue, colIdx }) {
      if (colIdx === (this.multiple ? 1 : 0)) return ''
      if (cellValue < this.redThreshold) return 'bg-danger-light'
      if (cellValue >= this.greenThreshold) return 'bg-success-light'
      return ''
    },
    qualiFor({ id }) {
      return this.qualiData?.qualis?.find(quali => quali.participant_id === id)
    },
  },
}
</script>

<style scoped>

</style>
