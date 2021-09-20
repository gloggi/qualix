<template>
  <form-basic :action="action" ref="form">
    <div class="mb-3" v-if="localRequirements.length">
      <h5>{{ $t('t.views.quali_content.requirements_status') }}</h5>
      <requirement-progress :requirements="localRequirements"></requirement-progress>
    </div>

    <div class="d-flex justify-content-between mb-2">
      <slot></slot>
      <span class="text-secondary btn">{{ autosaveText }} <i class="fas" :class="autosaveIcon"></i></span>
    </div>

    <input-quali-editor-large
      name="quali_contents"
      :course-id="courseId"
      autofocus
      v-model="json"
      :observations="observations"
      :requirements="requirements"
      :categories="categories"
      :show-requirements="showRequirements"
      :show-categories="showCategories"
      :show-impression="showImpression"
      :collaboration-key="collaborationKey"
      @localinput="debouncedAutosave()"></input-quali-editor-large>
  </form-basic>
</template>

<script>
import RequirementProgress from './RequirementProgress'
import QualiEditor from './QualiEditor'
import FormBasic from '../FormBasic'
import {debounce} from 'lodash'

export default {
  name: 'FormQualiContent',
  components: {FormBasic, RequirementProgress, QualiEditor},
  props: {
    action: {},
    courseId: { type: String, required: true },
    qualiContents: { type: Object, required: true },
    observations: { type: Array, default: () => [] },
    requirements: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    showRequirements: { type: Boolean, default: false },
    showCategories: { type: Boolean, default: false },
    showImpression: { type: Boolean, default: false },
    collaborationKey: { type: String, default: null },
  },
  data() {
    return {
      json: this.qualiContents,
      saving: false,
      debouncedAutosave: debounce(() => {
        this.saving = true
        this.$refs.form.xhrSubmit().then(() => {
          this.saving = false
        }).catch(() => {
          window.location.reload()
        })
      }, 2000)
    }
  },
  computed: {
    localRequirements() {
      return this.json.content
        .filter(node => node.type === 'requirement')
        .map(node => ({ pivot: node.attrs }))
    },
    autosaveText() {
      return this.saving ? this.$t('t.global.autosaving') : this.$t('t.global.autosaved')
    },
    autosaveIcon() {
      return this.saving ? 'fa-spinner' : 'fa-check'
    },
  }
}
</script>

<style scoped>

</style>
