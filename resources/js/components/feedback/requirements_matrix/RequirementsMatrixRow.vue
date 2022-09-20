<template>
  <b-tr>
    <td data-label="">
      <div class="d-flex align-items-baseline">
        <a :href="participantUrl"><img :src="participant.image_path" class="avatar-small" :alt="participant.scout_name"/></a>
        <div class="d-flex flex-column flex-grow-1">
          <div class="d-flex ml-2">
            <a :href="participantUrl"><strong>{{ participant.scout_name }}</strong></a>
            <span class="flex-grow-1"></span>
            <a :href="feedbackEditUrl" target="_blank" :title="$t(`t.views.feedback.requirements_matrix.edit_feedback`)"><i class="fas fa-pen-to-square px-2"></i></a>
            <a :href="feedbackPrintUrl" target="_blank" :title="$t('t.global.print')"><i class="fas fa-print pl-2"></i></a>
          </div>
          <div v-if="feedback.users.length > 0" class="mw-80 ml-2">{{ $t('t.models.feedback.users') }}: {{ feedback.users.map(u => u.name).join(', ') }}</div>
        </div>
      </div>
    </td>
    <td
      v-for="feedbackRequirement in feedbackRequirements"
      :key="feedbackRequirement.participant_id + ',' + feedbackRequirement.requirement_id"
      :class="cellClass(feedbackRequirement)"
      :data-label="cellLabel(feedbackRequirement)"
      @click="cellClicked(feedbackRequirement)"><requirements-matrix-cell
        :feedback="feedback"
        :feedback-requirement="feedbackRequirement"
        :requirement-statuses="requirementStatuses"
        @input="updateEditor" /></td>
  </b-tr>
</template>
<script>
import {Editor} from '@tiptap/vue-2'
import Document from '@tiptap/extension-document'
import Paragraph from '@tiptap/extension-paragraph'
import Text from '@tiptap/extension-text'
import Heading from '@tiptap/extension-heading'
import NodeObservation from '../tiptap-extensions/observation/NodeObservation'
import NodeRequirement from '../tiptap-extensions/requirement/NodeRequirement'
import RequirementsMatrixCell from './RequirementsMatrixCell'
import Collaboration from '@tiptap/extension-collaboration'
import * as Y from 'yjs'
import {WebrtcProvider} from 'y-webrtc'
import {sortBy} from 'lodash'

export default {
  name: 'RequirementsMatrixRow',
  components: { RequirementsMatrixCell },
  props: {
    feedback: {type: Object, required: true},
    allRequirements: {type: Array, required: true},
    requirementStatuses: {type: Array, required: true},
  },
  data: function () {
    const editor = this.createEditor()
    return {
      editor,
    }
  },
  computed: {
    feedbackRequirements() {
      const requirementNodes = []
      this.editor.state.doc.descendants(child => {
        if (child.type.name !== 'requirement') return false
        requirementNodes.push(child.attrs)
        return false
      })
      return sortBy(requirementNodes, 'id').map(attrs => ({
        requirement_id: attrs.id,
        requirement_status_id: attrs.status_id,
        comment: attrs.comment,
        participant_id: this.participant.id,
        requirement: this.allRequirements.find(requirement => String(requirement.id) === String(attrs.id))
      }))
    },
    participant() {
      return this.feedback.participant
    },
    participantUrl() {
      return this.routeUri('participants.detail', {
        course: this.feedback.feedback_data.course_id,
        participant: this.participant.id
      })
    },
    feedbackEditUrl() {
      return this.routeUri('feedbackContent.edit', {
        course: this.feedback.feedback_data.course_id,
        participant: this.participant.id,
        feedback: this.feedback.id
      })
    },
    feedbackPrintUrl() {
      return this.routeUri('feedbackContent.print', {
        course: this.feedback.feedback_data.course_id,
        participant: this.participant.id,
        feedback: this.feedback.id
      })
    },
  },
  methods: {
    cellClass(feedbackRequirement) {
      const status = this.requirementStatuses.find(status => String(status.id) === String(feedbackRequirement.requirement_status_id))
      const color = status?.color
      return `requirements-matrix-cell bg-${color} text-auto text-${color}-hover bg-auto-hover text-lg-center cursor-pointer`
    },
    cellLabel(feedbackRequirement) {
      return feedbackRequirement.requirement?.content
    },
    cellClicked(feedbackRequirement) {
      this.$bvModal.show(`requirement-matrix-cell-${feedbackRequirement.participant_id}-${feedbackRequirement.requirement_id}`)
    },
    createEditor() {
      if (!this.feedback.collaborationKey) return null

      return new Editor({
        content: this.feedback.contents ?? null,
        injectCSS: false,
        extensions: [
          Document,
          Paragraph,
          Text,
          Heading.configure({levels: [3, 5, 6]}),
          NodeObservation({readonly: false}).configure({readonly: false}),
          NodeRequirement({readonly: false}).configure({readonly: false}),
          ...this.createCollaborationExtension(),
        ],
      })
    },
    createCollaborationExtension() {
      if (!window.crypto.subtle || !this.feedback.collaborationKey) {
        // We are in an environment where crypto and thus syncing is not available
        // This currently happens only in the Cypress E2E tests
        return []
      }
      const ydoc = new Y.Doc()
      const feedbackKey = 'qualix-feedback-' + this.feedback.feedback_data.course_id + '-' + this.feedback.collaborationKey.substr(0, 8)
      new WebrtcProvider(feedbackKey, ydoc, {
        password: this.feedback.collaborationKey.substr(8),
        ...(this.signalingServers ? { signaling: this.signalingServers } : {})
      })
      return [Collaboration.configure({ document: ydoc })]
    },
    updateEditor(changes) {
      this.editor.commands.updateRequirementNode(changes)
    }
  }
}
</script>
