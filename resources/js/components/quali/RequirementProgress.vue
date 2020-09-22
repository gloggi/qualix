<template>
  <b-progress :max="100" height="1.5rem">
    <b-progress-bar variant="success" :value="100. * passed / total">
      {{ $tc('t.views.participant_details.qualis.requirements_passed', passed) }}
    </b-progress-bar>
    <b-progress-bar variant="danger" :value="100. * failed / total">
      {{ $tc('t.views.participant_details.qualis.requirements_failed', failed) }}
    </b-progress-bar>
  </b-progress>
</template>

<script>

  export default {
    name: 'RequirementProgress',
    props: {
      requirements: { type: Array, required: true }
    },
    data() {
      return {}
    },
    computed: {
      total() {
        return this.requirements.length
      },
      passed() {
        return this.requirements.filter(r => r.pivot.passed === 1).length
      },
      failed() {
        return this.requirements.filter(r => r.pivot.passed === 0).length
      }
    }
  }
</script>

<style scoped>

</style>
