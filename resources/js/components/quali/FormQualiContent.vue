<template>
  <form-basic :action="action" ref="form">
    <div class="mb-3" v-if="localRequirements.length">
      <h5>{{ $t('t.views.quali_content.requirements_status') }}</h5>
      <requirement-progress :requirements="localRequirements"></requirement-progress>
    </div>

    <div class="d-flex justify-content-between mb-2">
      <slot></slot>
      <span v-if="!offline" class="text-secondary btn">{{ autosaveText }} <i class="fas" :class="autosaveIcon"></i></span>
      <help-text v-else id="quali-editor-offline-help" class="text-right w-50" trans="t.views.quali_content.offline_help">
        <template #question><i class="fas fa-exclamation-triangle mr-2 text-danger"></i></template>
      </help-text>
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
import {debounce} from 'lodash'

export default {
  name: 'FormQualiContent',
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
      offline: false,
      debouncedAutosave: debounce(() => {
        this.saving = true
        this.offline = false
        this.$refs.form.xhrSubmit().then(() => {
          this.saving = false
        }).catch(err => {
          if (!err.response && err.request) {
            this.offline = true
          } else {
            window.location.reload()
          }
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
