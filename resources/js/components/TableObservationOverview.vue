<template>
  <responsive-table
    :data="participants"
    :actions="actions"
    :fields="fields"
    :cell-class="cellClass"></responsive-table>
</template>

<script>

import ResponsiveTable from "./ResponsiveTable"
export default {
  name: 'TableObservationOverview',
  components: {ResponsiveTable},
  props: {
    users: { type: Array, required: true },
    participants: { type: Array, required: true },
    multiple: { type: Boolean, default: false },
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
      return [
        {
          label: this.$t('t.models.observation.participants'),
          value: participant => participant.scout_name,
          href: participant => this.routeUri('participants.detail', { course: participant.course_id, participant: participant.id })
        },
        ...totalColumn,
        ...observationColumns,
      ]
    }
  },
  methods: {
    totalObservations(participant) {
      return this.users.reduce((sum, user) => sum + (participant.observation_counts_by_user[user.id] || 0), 0)
    },
    cellClass({ cellValue, colIdx }) {
      if (colIdx === (this.multiple ? 1 : 0)) return ''
      if (cellValue < 5) return 'bg-danger-light'
      if (cellValue >= 10) return 'bg-success-light'
      return ''
    }
  },
}
</script>

<style scoped>

</style>
