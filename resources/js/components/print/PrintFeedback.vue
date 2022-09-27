<template>
  <div>
    <div v-if="error">{{ error }}</div>
    <div v-else>{{ $t('t.views.feedback.print.pdf_is_being_generated') }}</div>
  </div>
</template>

<script>
import renderPdf from './feedback/index.js'
import sanitizeFilename from 'sanitize-filename'
import { saveAs } from 'file-saver'

export default {
  name: 'PrintFeedback',
  props: {
    course: { type: Object, required: true },
    feedback: { type: Object, required: true },
    feedbackContents: { type: Object, default: null },
    participant: { type: Object, required: true },
    observations: { type: Array, default: () => [] },
    statuses: { type: Array, default: () => [] },
  },
  data() {
    return {
      url: null,
      error: null,
    }
  },
  computed: {
    filename() {
      return sanitizeFilename(this.feedback.name + '-' + this.participant.scout_name + '.pdf', { replacement: '_' })
    },
  },
  mounted() {
    this.generatePdf()
  },
  unmounted() {
    this.revokeOldObjectUrl()
  },
  methods: {
    async generatePdf() {
      this.error = null
      this.revokeOldObjectUrl()

      const { error, blob } = await renderPdf({
        course: this.course,
        feedback: this.feedback,
        feedbackContents: this.feedbackContents,
        participant: this.participant,
        observations: this.observations,
        statuses: this.statuses,
      }, document.documentElement.lang)

      if (error) {
        this.error = error
        console.error(error)
      } else {
        this.url = URL.createObjectURL(blob)
        console.log(saveAs, this.url, this.filename)
        saveAs(this.url, this.filename)
        setTimeout(() => window.close(), 100)
      }
    },
    revokeOldObjectUrl() {
      const oldUrl = this.url
      if (oldUrl) {
        this.url = null
        URL.revokeObjectURL(oldUrl)
      }
    },
  },
}
</script>

<style scoped></style>
