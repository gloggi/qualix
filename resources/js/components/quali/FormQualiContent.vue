<template>
  <form-basic :action="action">
    <div class="mb-3" v-if="localRequirements.length">
      <h5>{{ $t('t.views.quali_content.requirements_status') }}</h5>
      <requirement-progress :requirements="localRequirements"></requirement-progress>
    </div>

    <div class="d-flex justify-content-between mb-2">
      <slot></slot>
      <button type="submit" class="btn btn-primary">{{ $t('t.global.save')}}</button>
    </div>

    <input-quali-editor-large
      name="quali_contents"
      :course-id="courseId"
      autofocus
      v-model="json"
      :observations="observations"
      :requirements="requirements"
      :categories="categories"></input-quali-editor-large>
  </form-basic>
</template>

<script>
import RequirementProgress from './RequirementProgress'
import QualiEditor from './QualiEditor'
import FormBasic from "../FormBasic"

export default {
  name: 'FormQualiContent',
  components: {FormBasic, RequirementProgress, QualiEditor},
  props: {
    action: {},
    courseId: { type: String },
    qualiContents: { type: Object, required: true },
    observations: { type: Array, default: () => [] },
    requirements: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
  },
  data() {
    return {
      json: this.qualiContents,
    }
  },
  computed: {
    localRequirements() {
      return this.json.content
        .filter(node => node.type === 'requirement')
        .map(node => ({ pivot: node.attrs }))
    }
  }
}
</script>

<style scoped>

</style>
