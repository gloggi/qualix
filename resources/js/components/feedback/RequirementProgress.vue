<template>
  <b-progress :max="100">
    <b-progress-bar v-for="(requirements, status_id) in groupedRequirements" :key="status_id" :variant="status(status_id).color" :value="100. * (requirements.length) / total">
      {{ requirements.length }} {{ status(status_id).name }}
    </b-progress-bar>
  </b-progress>
</template>

<script>
import {groupBy} from 'lodash'

export default {
  name: 'RequirementProgress',
  props: {
    requirements: { type: Array, required: true },
    statuses: { type: Array, required: true },
  },
  data() {
    return {}
  },
  computed: {
    total() {
      return this.requirements.length
    },
    groupedRequirements() {
      return groupBy(this.requirements, 'status_id')
    },
  },
  methods: {
    status(id) {
      return this.statuses.find(status => String(status.id) === String(id))
    }
  }
}
</script>

<style scoped>

</style>
