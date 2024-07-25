<template>
  <form-basic :action="action" ref="form">
    <div class="mb-3" v-if="localRequirements.length">
      <h5>{{ $t('t.views.feedback_content.requirements_status') }}</h5>
      <requirement-progress :requirements="localRequirements" :statuses="requirementStatuses"></requirement-progress>
    </div>

    <div class="d-flex justify-content-between mb-2">
      <slot></slot>
      <auto-save ref="autosave" trans="t.views.feedback_content" :form="form" text-class="text-right w-50" @error="error=true" />
    </div>

    <input-feedback-editor-large
      name="feedback_contents"
      :course-id="courseId"
      :feedback-data-id="feedbackDataId"
      autofocus
      v-model="json"
      :observations="observations"
      :requirements="requirements"
      :categories="categories"
      :authors="authors"
      :blocks="blocks"
      :requirement-statuses="requirementStatuses"
      :show-requirements="showRequirements"
      :show-categories="showCategories"
      :show-impression="showImpression"
      :collaboration-key="collaborationKey"
      :mark-invalid="error"
      @localinput="onLocalInput"></input-feedback-editor-large>
  </form-basic>
</template>

<script>
import AutoSave from '../AutoSave'
import FormBasic from '../FormBasic'
import RequirementProgress from './RequirementProgress'
import InputFeedbackEditorLarge from './InputFeedbackEditorLarge'

export default {
  name: 'FormFeedbackContent',
  components: {InputFeedbackEditorLarge, RequirementProgress, FormBasic, AutoSave },
  props: {
    action: {},
    courseId: { type: String, required: true },
    feedbackDataId: { type: String, required: false },
    feedbackContents: { type: Object, required: true },
    observations: { type: Array, default: () => [] },
    requirements: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    authors: { type: Array, default: () => [] },
    blocks: { type: Array, default: () => [] },
    requirementStatuses: { type: Array, default: () => [] },
    evaluationGrids: { type: Array, default: () => [] },
    showRequirements: { type: Boolean, default: false },
    showCategories: { type: Boolean, default: false },
    showImpression: { type: Boolean, default: false },
    collaborationKey: { type: String, default: null },
  },
  data() {
    return {
      json: this.feedbackContents,
      error: false,
    }
  },
  provide() {
    return {
      evaluationGrids: this.evaluationGrids,
    }
  },
  computed: {
    localRequirements() {
      return this.json.content
        .filter(node => node.type === 'requirement')
        .map(node => node.attrs)
    },
    form() {
      return () => this.$refs.form
    },
  },
  methods: {
    onLocalInput(...args) {
      this.error = false
      this.$refs.autosave.onInput(...args)
    }
  }
}
</script>

<style scoped>

</style>
