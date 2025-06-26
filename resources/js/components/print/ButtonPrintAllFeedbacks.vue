<template>
  <a
    class="btn btn-primary"
    v-b-tooltip
    :title="tooltip"
    @click="generateAndDownloadPdfs">
    <slot>
      <i class="fas fa-print" />
    </slot>
  </a>
</template>

<script>
import sanitizeFilename from 'sanitize-filename'
import { saveAs } from 'file-saver'
import JSZip from 'jszip';

export default {
  name: 'ButtonPrintAllFeedbacks',
  props: {
    feedbacks: { type: Array, required: true },
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
      totalParticipants: 0,
      completedPDFs: 0,
    }
  },
  computed: {
    filename() {
      if (!this.feedback || !this.participant) {
        return 'qualix.pdf'
      }
      return sanitizeFilename(this.feedback?.name + '.zip', { replacement: '_' })
    },
    tooltip() {
      if (!this.clicked) return this.$t('t.global.print')
      if (this.error) return this.error
      return this.url ? this.$t('t.views.feedback.print.pdf_downloaded') : this.$t('t.views.feedback.print.pdf_is_being_generated') + " (" + this.completedPDFs + "/" + this.totalParticipants + ")"
    },
  },
  unmounted() {
    this.revokeOldObjectUrl()
  },
  methods: {
    async generateAndDownloadPdfs() {
      this.clicked = true
      this.error = null
      this.totalParticipants = this.feedbacks.length;
      this.completedPDFs = 0;

      await this.generatePdfs()
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
        if (error.response?.status === 401 || error.response?.status === 404) {
          window.location.reload()
          return
        }
        console.error('Error fetching feedback data for printing:', JSON.stringify(error))
        this.error = this.$t('t.views.feedback.print.error_fetching_data')
      })
    },
    async generatePdfs() {
      this.error = null
      this.revokeOldObjectUrl()

      // Only load the whole react chain once we really need it
      const renderPdf = (await import('./feedback/index.js')).default

      const resultZip = new JSZip();

      for (const feedback of this.feedbacks) {
        this.courseId = feedback['feedback_data']['course_id'];
        this.participantId = feedback['participant_id'];
        this.feedbackId = feedback.id;

        await this.fetchData();
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
          resultZip.file(sanitizeFilename(this.participant?.scout_name + '.pdf', { replacement: '_' }), blob);
          this.completedPDFs = this.completedPDFs + 1;
        }
      }

      if (!this.error) {
        this.url = URL.createObjectURL(await resultZip.generateAsync({type: "blob"}));
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
