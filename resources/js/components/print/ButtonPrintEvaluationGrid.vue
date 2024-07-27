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
  name: 'ButtonPrintEvaluationGrid',
  props: {
    courseId: { type: Number, required: true },
    evaluationGridTemplateId: { type: Number, required: true },
    evaluationGridId: { type: Number, default: null },
  },
  data() {
    return {
      clicked: false,
      url: null,
      error: null,
      course: null,
      evaluationGridTemplate: null,
      evaluationGrid: null,
    }
  },
  computed: {
    participants() {
      return this.evaluationGrid?.participants || []
    },
    block() {
      return this.evaluationGrid?.block
    },
    user() {
      return this.evaluationGrid?.user
    },
    filename() {
      if (!this.evaluationGridTemplate) {
        return 'qualix.pdf'
      }
      if (!this.evaluationGrid || !this.block || !this.participants.length) {
        return sanitizeFilename(this.evaluationGridTemplate?.name + '.pdf', { replacement: '_' })
      }
      return sanitizeFilename(this.evaluationGridTemplate?.name + '-' + this.block?.blockname_and_number + '-' + this.participants.map(p => p.scout_name).join('-') + '.pdf', { replacement: '_' })
    },
    tooltip() {
      if (!this.clicked) return this.$t('t.global.print')
      if (this.error) return this.error
      return this.url ? this.$t('t.views.evaluation_grids.print.pdf_downloaded') : this.$t('t.views.evaluation_grids.print.pdf_is_being_generated')
    },
  },
  unmounted() {
    this.revokeOldObjectUrl()
  },
  methods: {
    async generateAndDownloadPdf() {
      this.clicked = true
      this.error = null

      await this.fetchData()
      if (!this.evaluationGridTemplate) return

      await this.generatePdf()
      if (!this.url) return

      saveAs(this.url, this.filename)
    },
    async fetchData() {
      this.course = null
      this.evaluationGrid = null
      const url = this.evaluationGridId ?
        this.routeUri('evaluationGrid.print', { course: this.courseId, evaluation_grid_template: this.evaluationGridTemplateId, evaluation_grid: this.evaluationGridId }) :
        this.routeUri('admin.evaluation_grid_templates.print', { course: this.courseId, evaluation_grid_template: this.evaluationGridTemplateId })
      return window.axios({ url }).then(response => {
        if (response.status !== 200) {
          console.error('Unexpected response status when fetching evaluation grid data for printing:', JSON.stringify(response))
          this.error = this.$t('t.views.evaluation_grids.print.error_fetching_data')
          return
        }
        this.course = response.data.course
        this.evaluationGridTemplate = response.data.evaluationGridTemplate
        this.evaluationGrid = response.data.evaluationGrid || null
      }).catch(error => {
        if (error.response?.status === 401 || error.response?.status === 404) {
          window.location.reload()
          return
        }
        console.error('Error fetching evaluation grid data for printing:', JSON.stringify(error))
        this.error = this.$t('t.views.evaluation_grids.print.error_fetching_data')
      })
    },
    async generatePdf() {
      this.error = null
      this.revokeOldObjectUrl()

      // Only load the whole react chain once we really need it
      const renderPdf = (await import('./evaluationGrid/index.js')).default
      const { error, blob } = await renderPdf({
        course: this.course,
        evaluationGridTemplate: this.evaluationGridTemplate,
        evaluationGrid: this.evaluationGrid,
        participants: this.participants,
        block: this.block,
        user: this.user,
      }, document.documentElement.lang)

      if (error) {
        console.error('Error creating pdf for evaluation grid:', error,
          'course id:', this.courseId,
          'evaluation grid template id:', this.evaluationGridTemplateId,
          'evaluation grid id:', this.evaluationGridId,
          'data used for creating the pdf:',
          this.course, this.evaluationGrid, this.evaluationGridTemplate, this.participants, this.block)
        this.error = this.$t('t.views.evaluation_grids.print.error_creating_pdf')
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
