<template>
  <form-basic :action="action">
    <div class="mb-3" v-if="localRequirements.length">
      <h5>{{ $t('t.views.quali_content.requirements_status') }}</h5>
      <requirement-progress :requirements="localRequirements"></requirement-progress>
    </div>

    <div class="mb-2">
      <button type="submit" class="btn btn-primary">{{ $t('t.global.save')}}</button>
    </div>

    <quali-editor
      ref="qualiContents"
      name="qualiContents"
      :course-id="courseId"
      autofocus
      v-model="json"
      :observations="observations"
      :requirements="requirements"
      :categories="categories"></quali-editor>
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
      quali: { type: Object, required: true },
      observations: { type: Array, default: () => [] },
      requirements: { type: Array, default: () => [] },
      categories: { type: Array, default: () => [] },
    },
    data() {
      return {
        json: this.quali.contents,
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
