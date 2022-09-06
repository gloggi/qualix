<template>
  <div>
    <img :src="participant.image_path" class="avatar-small" :alt="participant.scout_name"/> <strong>{{ participant.scout_name }}</strong>
  </div>
</template>
<script>
import {Editor} from '@tiptap/vue-2'
import Document from '@tiptap/extension-document'
import Paragraph from '@tiptap/extension-paragraph'
import Text from '@tiptap/extension-text'
import Heading from '@tiptap/extension-heading'
import NodeObservation from './feedback/tiptap-extensions/observation/NodeObservation'
import NodeRequirement from './feedback/tiptap-extensions/requirement/NodeRequirement'
import Collaboration from '@tiptap/extension-collaboration'
import * as Y from 'yjs'
import {WebrtcProvider} from 'y-webrtc'

export default {
  name: 'RequirementsMatrixRow',
  props: {
    feedback: {type: Object, required: true},
    feedbackRequirements: {type: Array, default: null},
  },
  data: function () {
    const editor = this.createEditor()
    return {
      editor,
    }
  },
  computed: {
    participant() {
      return this.feedback.participant
    },
    currentValue() {
      return this.editor.getJSON()
        .content
        .filter(node => node.type === 'requirement')
        .map(node => node.attrs)
    }
  },
  methods: {
    onlyRequirements(editorContent) {
      if (!editorContent) return []
      return editorContent.content
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
          this.createCollaborationExtension()
        ],
        onCreate: ({ editor }) => {
          let creating = true
          editor.on('update', ({transaction}) => {
            this.$emit('input', this.currentValue)
            // onUpdate is also called while creating the editor, so filter that call out
            if (this.isRemoteChange(transaction) && !creating) {
              this.$emit('remoteinput', this.currentValue)
            }
            creating = false
          })
        }
      })
    },
    createCollaborationExtension() {
      const ydoc = new Y.Doc()
      const feedbackKey = 'qualix-feedback-' + this.feedback.feedback_data.course_id + '-' + this.feedback.collaborationKey.substr(0, 8)
      new WebrtcProvider(feedbackKey, ydoc, {
        password: this.feedback.collaborationKey.substr(8),
        ...(this.signalingServers ? { signaling: this.signalingServers } : {})
      })
      return Collaboration.configure({ document: ydoc })
    },
    isRemoteChange(transaction) {
      return transaction?.meta && Object.hasOwnProperty.apply(transaction.meta, ['y-sync$'])
    },
  },
  watch: {
    feedbackRequirements: {
      deep: true,
      handler() {
        // Since we cannot specify a watcher on all elements of an array, we instead have to filter out watcher
        // calls without real changes on our own
        const changes = this.feedbackRequirements.filter(feedbackRequirement => {
          const fromEditor = this.currentValue.find(editorFR => String(editorFR.id) === String(feedbackRequirement.requirement_id))
          return fromEditor && String(fromEditor.status_id) !== String(feedbackRequirement.requirement_status_id)
        })
        if (changes.length === 0) return

        this.editor.state.doc.descendants(child => {
          if (child.type.name !== 'requirement') return false
          const changedFeedbackRequirement = changes.find(fr => String(fr.requirement_id) === String(child.attrs.id))
          if (!changedFeedbackRequirement) return false
          this.editor.commands.setRequirementStatus(changedFeedbackRequirement)
          return false
        })

      },
    }
  }
}
</script>
