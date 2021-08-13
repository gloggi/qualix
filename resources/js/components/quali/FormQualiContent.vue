<template>
  <form-basic :action="action" @submit.prevent="onSubmit">
    <b-alert v-if="restoreWarning" variant="warning" show dismissible fade>
      {{ restoreWarning }}
    </b-alert>

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
      :categories="categories"
      :show-requirements="showRequirements"
      :show-categories="showCategories"
      :show-impression="showImpression"
      @input="onInput"></input-quali-editor-large>
  </form-basic>
</template>

<script>
import RequirementProgress from './RequirementProgress'
import QualiEditor from './QualiEditor'
import FormBasic from '../FormBasic'
import {get, isEqual} from 'lodash'

export default {
  name: 'FormQualiContent',
  components: {FormBasic, RequirementProgress, QualiEditor},
  props: {
    action: {},
    courseId: { type: String },
    qualiId: { type: String },
    qualiContents: { type: Object, required: true },
    observations: { type: Array, default: () => [] },
    requirements: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    showRequirements: { type: Boolean, default: false },
    showCategories: { type: Boolean, default: false },
    showImpression: { type: Boolean, default: false },
  },
  data() {
    return {
      json: this.qualiContents,
      restoreWarning: undefined,
    }
  },
  computed: {
    localRequirements() {
      return this.json.content
        .filter(node => node.type === 'requirement')
        .map(node => ({ pivot: node.attrs }))
    },
    storageKey() {
      return 'quali' + this.qualiId
    }
  },
  methods: {
    onInput() {
      this.saveToLocalStorage()
    },
    async onSubmit({ target: form }) {
      this.saveToLocalStorage()
      await window.axios.post(window.Laravel.routes['csrf.check'].uri).then(() => {}).catch((error) => {
        if (error.response.status === 419) {
          // Don't clear the stored form value when the request fails with 419 Page Expired.
          // This is the case when the CSRF token expired or the user logged out and back in
          // in another tab. This is the exact case when we need the stored form value.
          this.disableAutoClearLocalStorage()
        }
      })
      form.submit()
    },
    saveToLocalStorage() {
      localStorage.setItem(this.storageKey, JSON.stringify(this.json))
    },
    clearLocalStorage() {
      localStorage.removeItem(this.storageKey)
    },
    loadFromLocalStorage() {
      const stored = localStorage.getItem(this.storageKey)
      if (!stored) return

      const value = JSON.parse(stored)
      // Avoid showing the warning when nothing really changed
      if (isEqual(this.json, value)) return

      this.json = JSON.parse(stored)
      this.restoreWarning = this.$t('t.views.quali_content.form_restored')
    },
    enableAutoClearLocalStorage() {
      window.onbeforeunload = this.clearLocalStorage
    },
    disableAutoClearLocalStorage() {
      window.onbeforeunload = undefined
    }
  },
  mounted () {
    this.enableAutoClearLocalStorage()

    if (JSON.parse(get(window.Laravel.oldInput, 'quali_contents', 'false'))) {
      // If there is old input, keep it, it might be more up to date because it must have come
      // directly from the user submitting a form
      this.saveToLocalStorage()
    } else {
      // Otherwise if there is a locally stored value, load that
      this.loadFromLocalStorage()
    }
  }
}
</script>

<style scoped>

</style>
