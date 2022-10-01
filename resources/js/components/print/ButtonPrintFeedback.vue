<template>
  <a
    class="secondary"
    v-b-tooltip
    :title="tooltip"
    @click="generateAndDownloadPdf">
    <slot>
      <i class="fas fa-print" />
    </slot>
  </a>
</template>

<script>
import sanitizeFilename from 'sanitize-filename'
import { saveAs } from 'file-saver'

export default {
  name: 'ButtonPrintFeedback',
  props: {
    courseId: { type: Number, required: true },
    participantId: { type: Number, required: true },
    feedbackId: { type: Number, required: true },
  },
  data() {
    return {
      clicked: false,
      url: null,
      error: null,
      course: null,
      feedback: null,
      feedbackContents: null,
      participant: null,
      observations: null,
      statuses: null,
    }
  },
  computed: {
    filename() {
      if (!this.feedback || !this.participant) {
        return 'qualix.pdf'
      }
      return sanitizeFilename(this.feedback?.name + '-' + this.participant?.scout_name + '.pdf', { replacement: '_' })
    },
    tooltip() {
      if (!this.clicked) return this.$t('t.global.print')
      return this.error ||
      this.url ? this.$t('t.views.feedback.print.pdf_downloaded') : this.$t('t.views.feedback.print.pdf_is_being_generated')
    },
  },
  unmounted() {
    this.revokeOldObjectUrl()
  },
  methods: {
    async generateAndDownloadPdf() {
      this.clicked = true

      await this.fetchData()
      if (!this.feedback) return

      await this.generatePdf()
      if (!this.url) return

      saveAs(this.url, this.filename)
    },
    async fetchData() {
      this.course = null
      this.feedback = null
      this.feedbackContents = null
      this.participant = null
      this.observations = null
      this.statuses = null
      return window.axios({
        url: this.routeUri('feedbackContent.print', { course: this.courseId, participant: this.participantId, feedback: this.feedbackId }),
      }).then(response => {
        if (response.status !== 200) {
          console.error('Unexpected response status when fetching feedback data for printing:', JSON.stringify(response))
          this.error = this.$t('t.views.feedback.print.error_fetching_data')
          return
        }
        this.course = response.data.course
        this.feedback = response.data.feedback
        this.feedbackContents = response.data.feedbackContents
        this.participant = response.data.participant
        this.observations = response.data.observations
        this.statuses = response.data.statuses
      }).catch(error => {
        if (error.response?.status === 401) {
          window.location.reload()
          return
        }
        console.error('Error fetching feedback data for printing:', JSON.stringify(error))
        this.error = this.$t('t.views.feedback.print.error_fetching_data')
      })
    },
    async generatePdf() {
      this.error = null
      this.revokeOldObjectUrl()

      // Only load the whole react chain once we really need it
      const renderPdf = (await import('./feedback/index.js')).default
      const { error, blob } = await renderPdf({
        course: this.course,
        feedback: this.feedback,
        feedbackContents: this.feedbackContents,
        participant: this.participant,
        observations: this.observations,
        statuses: this.statuses,
      }, document.documentElement.lang)

      if (error) {
        console.error('Error creating pdf for feedback:', error,
          'course id:', this.courseId,
          'participant id:', this.participantId,
          'feedback id:', this.feedbackId,
          'data used for creating the pdf:',
          this.course, this.feedback, this.feedbackContents, this.participant, this.observations, this.statuses)
        this.error = this.$t('t.views.feedback.print.error_creating_pdf')
      } else {
        this.url = URL.createObjectURL(blob)
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
