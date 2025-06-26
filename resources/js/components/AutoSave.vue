<template>
  <help-text v-if="offline" id="feedback-editor-offline-help" :class="textClass" :trans="`${trans}.offline_help`">
    <template #question><i class="fas fa-triangle-exclamation mr-2 text-danger"></i></template>
  </help-text>
  <help-text v-else-if="loggedOut" id="feedback-editor-logged-out-help" :class="textClass" :trans="`${trans}.logged_out_help`">
    <template #question><i class="fas fa-triangle-exclamation mr-2 text-danger"></i></template>
    {{ $t(`${trans}.logged_out_help.answer`) }} <a href="#" @click.prevent="refreshCsrf">{{ $t(`${trans}.logged_out_help.click_here_to_log_back_in`) }}</a>
  </help-text>
  <span v-else class="btn px-0 text-secondary" :class="textClass">{{ autosaveText }} <i class="fas" :class="autosaveIcon"></i></span>
</template>
<script>
import debounce from 'lodash/debounce'
import HelpText from './HelpText.vue'

export default {
  name: 'AutoSave',
  components: {HelpText},
  props: {
    trans: { type: String, required: true },
    form: { type: Function, required: true },
    textClass: { type: String, default: '' },
  },
  data: function() {
    return {
      saving: false,
      offline: false,
      loggedOut: false,
      dirty: false,
      debouncedAutosave: debounce(this.autosave, 2000)
    }
  },
  computed: {
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
    onInput () {
      this.dirty = true
      this.debouncedAutosave()
    },
    autosave () {
      const previouslyOffline = this.offline
      this.saving = true
      this.offline = false
      this.loggedOut = false
      this.dirty = false
      this.form().xhrSubmit().then(() => {
        this.saving = false
        if (previouslyOffline) window.dispatchEvent(new Event('online'))
      }).catch(err => {
        if (!err.response && err.request) {
          this.offline = true
          this.$emit('error')
          window.dispatchEvent(new Event('offline'))
        } else if (err.response && err.response.status === 419) {
          this.loggedOut = true
          this.$emit('error')
        } else {
          console.log(err)
          //window.location.reload()
        }
      })
    }
  }
}
</script>
