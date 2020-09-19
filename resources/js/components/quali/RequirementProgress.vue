<template>
  <b-progress :max="100" height="1.5rem">
    <b-progress-bar v-if="passed > 0" variant="success" :value="100. * passed / total">
      {{ $tc('t.views.participant_details.qualis.requirements_passed', passed) }}
    </b-progress-bar>
    <b-progress-bar v-if="failed > 0" variant="danger" :value="100. * failed / total">
      {{ $tc('t.views.participant_details.qualis.requirements_failed', failed) }}
    </b-progress-bar>
  </b-progress>
</template>

<script>

  export default {
    name: 'RequirementProgress',
    props: {
      qualiRequirements: { type: Array, required: true }
    },
    data() {
      return {}
    },
    computed: {
      total() {
        return this.qualiRequirements.length
      },
      passed() {
        return this.qualiRequirements.filter(r => r.passed === 1).length
      },
      failed() {
        return this.qualiRequirements.filter(r => r.passed === 0).length
      }
    }
  }
</script>

<style scoped>

</style>
