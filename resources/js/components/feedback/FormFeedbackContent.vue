<template>
  <form-basic :action="action" ref="form">
    <div class="mb-3" v-if="localRequirements.length">
      <h5>{{ $t('t.views.feedback_content.requirements_status') }}</h5>
      <requirement-progress :requirements="localRequirements"></requirement-progress>
    </div>

    <div class="d-flex justify-content-between mb-2">
      <slot></slot>
      <help-text v-if="offline" id="feedback-editor-offline-help" class="text-right w-50" trans="t.views.feedback_content.offline_help">
        <template #question><i class="fas fa-exclamation-triangle mr-2 text-danger"></i></template>
      </help-text>
      <help-text v-else-if="loggedOut" id="feedback-editor-logged-out-help" class="text-right w-50" trans="t.views.feedback_content.logged_out_help">
        <template #question><i class="fas fa-exclamation-triangle mr-2 text-danger"></i></template>
        {{ $t('t.views.feedback_content.logged_out_help.answer') }} <a href="#" @click.prevent="refreshCsrf">{{ $t('t.views.feedback_content.logged_out_help.click_here_to_log_back_in') }}</a>
      </help-text>
      <span v-else class="text-secondary btn">{{ autosaveText }} <i class="fas" :class="autosaveIcon"></i></span>
    </div>

    <input-feedback-editor-large
      name="feedback_contents"
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
      :mark-invalid="offline || loggedOut"
      @localinput="onLocalInput()"></input-feedback-editor-large>
  </form-basic>
</template>

<script>
import {debounce} from 'lodash'

export default {
  name: 'FormFeedbackContent',
  props: {
    action: {},
    courseId: { type: String, required: true },
    feedbackContents: { type: Object, required: true },
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
      json: this.feedbackContents,
      saving: false,
      offline: false,
      loggedOut: false,
      dirty: false,
      debouncedAutosave: debounce(this.autosave, 2000)
    }
  },
  computed: {
    localRequirements() {
      return this.json.content
        .filter(node => node.type === 'requirement')
        .map(node => ({ pivot: node.attrs }))
    },
    autosaveText() {
      return this.dirty ? this.$t('t.global.autosave_paused') : this.saving ? this.$t('t.global.autosaving') : this.$t('t.global.autosaved')
    },
    autosaveIcon() {
      return this.dirty ? 'fa-pause' : this.saving ? 'fa-spinner' : 'fa-check'
    },
  },
  methods: {
    refreshCsrf () {
      window.updateCsrf = csrf => {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrf;
        // No need to debounce this, we want immediate saving here
        this.autosave()
        window.updateCsrf = undefined
      }
      window.open(this.routeUri('refreshCsrf'))
    },
    onLocalInput () {
      this.dirty = true
      this.debouncedAutosave()
    },
    autosave () {
      this.saving = true
      this.offline = false
      this.loggedOut = false
      this.dirty = false
      this.$refs.form.xhrSubmit().then(() => {
        this.saving = false
      }).catch(err => {
        if (!err.response && err.request) {
          this.offline = true
        } else if (err.response && err.response.status === 419) {
          this.loggedOut = true
        } else {
          window.location.reload()
        }
      })
    }
  }
}
</script>

<style scoped>

</style>
