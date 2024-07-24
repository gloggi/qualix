<template>
  <Transition>
    <div v-if="templates.length">
      <slot></slot>
      <b-button v-if="single" :href="routeUri('evaluationGrid.new', { course: courseId, evaluation_grid_template: templates[0].id, block: blockId })" variant="link" class="py-0 px-2 align-baseline">
        <i class="fas fa-list-check"></i> {{ templates[0].name }}
      </b-button>
      <b-dropdown v-else variant="link" class="align-baseline" toggle-class="py-0">
        <template #button-content><i class="fas fa-list-check"></i> {{ $t('t.models.evaluation_grid.model_name') }}</template>
        <b-dropdown-item
          v-for="evaluationGridTemplate in templates"
          :key="evaluationGridTemplate.id"
          :href="routeUri('evaluationGrid.new', { course: courseId, evaluation_grid_template: evaluationGridTemplate.id, block: blockId })">
          <i class="fas fa-list-check"></i> {{ evaluationGridTemplate.name }}
        </b-dropdown-item>
      </b-dropdown>
    </div>
  </Transition>
</template>

<script>

export default {
  name: 'BlockAndRequirementsInputWrapper',
  props: {
    evaluationGridTemplatesMapping: { type: Object, default: () => ({}) },
    courseId: { type: Number, required: true },
    blockId: { type: String, required: false },
  },
  computed: {
    templates() {
      if (!this.blockId) return []
      return this.evaluationGridTemplatesMapping[this.blockId] || []
    },
    single() {
      return this.templates.length === 1
    },
  },
}
</script>

<style scoped>

</style>
