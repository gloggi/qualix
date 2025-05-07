<template>
  <div class="editor" :class="{ 'focus': focused }">
    <modal-add-observation v-if="observations.length" :observations="observations" v-model="addObservation" :return-focus="{ $el: { focus: () => editor.commands.focus() } }" :used-observations="usedObservationIds" :show-requirements="showRequirements" :show-categories="showCategories" :show-impression="showImpression"></modal-add-observation>
    <input-hidden v-if="name" :value="formValue" :name="name"></input-hidden>
    <editor-floating-menu v-if="!readonly && editor" :editor="editor" :tippy-options="{ zIndex: 1 }">
      <floating-menu :observations="observations" :editor="editor" @addObservation="showObservationSelectionModal(true)"/>
    </editor-floating-menu>
    <b-alert v-if="offline" class="offline-warning-banner" variant="danger" show fade>
      <help-text v-if="offline" id="feedback-editor-offline-help" trans="t.views.feedback_content.offline_help_banner">
        <template #question><i class="fas fa-triangle-exclamation mr-2 text-danger"></i></template>
      </help-text>
    </b-alert>
    <editor-content class="editor-content" :class="{ readonly }" :editor="editor" />
  </div>
</template>

<script>
import {cloneDeep, isEqual, sortBy} from 'lodash'
import {Editor, EditorContent, FloatingMenu as EditorFloatingMenu} from '@tiptap/vue-2'
import Document from '@tiptap/extension-document'
import Paragraph from '@tiptap/extension-paragraph'
import Text from '@tiptap/extension-text'
import Heading from '@tiptap/extension-heading'
import History from '@tiptap/extension-history'
import GapCursor from '@tiptap/extension-gapcursor'
import DropCursor from '@tiptap/extension-dropcursor'
import Collaboration from '@tiptap/extension-collaboration'
import CollaborationCursor from '@tiptap/extension-collaboration-cursor'
import * as Y from 'yjs'
import {WebrtcProvider} from 'y-webrtc'
import NodeObservation from './tiptap-extensions/observation/NodeObservation.js'
import NodeRequirement from './tiptap-extensions/requirement/NodeRequirement.js'
import GapCursorFocus from './tiptap-extensions/GapCursorFocus'
import InputHidden from '../form/InputHidden'
import FloatingMenu from './FloatingMenu'
import ModalAddObservation from './tiptap-extensions/observation/ModalAddObservation'
import HelpText from '../HelpText.vue';

export default {
  name: 'FeedbackEditor',
  components: { HelpText, FloatingMenu, InputHidden, EditorContent, EditorFloatingMenu, ModalAddObservation},
  props: {
    name: { type: String },
    courseId: { type: String, required: true },
    feedbackDataId: { type: String, required: false },
    value: { type: Object, default: null },
    observations: { type: Array, default: () => [] },
    requirements: { type: Array, required: true },
    feedbackRequirements: { type: Array, default: null },
    categories: { type: Array, default: () => [] },
    authors: { type: Array, default: () => [] },
    blocks: { type: Array, default: () => [] },
    requirementStatuses: { type: Array, default: () => [] },
    readonly: { type: Boolean, default: false },
    autofocus: { type: Boolean, default: false },
    showRequirements: { type: Boolean, default: false },
    showCategories: { type: Boolean, default: false },
    showImpression: { type: Boolean, default: false },
    username: { type: String, default: null },
    collaborationKey: { type: String, default: null },
  },
  data() {
    const extensions = [
      Document,
      Paragraph,
      Text,
      Heading.configure({ levels: [ 3, 5, 6 ] }),
      NodeObservation({ readonly: this.readonly }).configure({ readonly: this.readonly }),
      NodeRequirement({ readonly: this.readonly }).configure({ readonly: this.readonly }),
      GapCursor,
      GapCursorFocus,
      DropCursor,
    ]
    const collaborationSupported = this.collaborationKey && window.crypto.subtle
    const editor = new Editor({
      content: this.value ?? null,
      editable: !this.readonly,
      injectCSS: false,
      autofocus: this.autofocus,
      extensions: collaborationSupported ? this.withCollaboration(extensions) : this.withHistory(extensions),
      onBlur: () => {
        this.focused = false
      },
      onFocus: () => {
        this.focused = true
      },
      onCreate: ({ editor }) => {
        let creating = true
        // Ensure editor is fully loaded before setting creating to false
        editor.on('transaction', () => {
          if (creating) {
            this.$nextTick(() => {
              creating = false;
            });
          }
        });
        editor.on('update', ({editor, transaction}) => {
          this.currentValue = editor.getJSON()
          this.$emit('input', this.currentValue)
          // onUpdate is also called while creating the editor, so filter that call out
          if (!this.isRemoteChange(transaction) && !creating) {
            this.$emit('localinput', this.currentValue)
          }
        })
      }
    })
    const emptyDocument = editor.getJSON()

    return {
      editor: editor,
      currentValue: this.value ?? emptyDocument,
      emptyDocument,
      focused: false,
      addObservation: null,
      offline: false,
    }
  },
  computed: {
    formValue() {
      return JSON.stringify(this.currentValue)
    },
    getEmptyParagraph() {
      return () => cloneDeep(this.emptyDocument.content[0])
    },
    usedObservationIds() {
      return this.currentValue.content
        .filter(node => node.type === 'observation')
        .map(node => node.attrs.id)
    },
    defaultRequirementStatusId() {
      if (!this.requirementStatuses.length) return
      return this.requirementStatuses[0].id
    }
  },
  methods: {
    withCollaboration (extensions) {
      const ydoc = new Y.Doc()
      const feedbackKey = 'qualix-feedback-' + this.courseId + '-' + this.collaborationKey.substr(0, 8)
      const signalingServers = window.Laravel.signalingServers
      const provider = new WebrtcProvider(feedbackKey, ydoc, {
        password: this.collaborationKey.substr(8),
        ...(signalingServers ? { signaling: signalingServers } : {}),
      })
      return extensions.concat([
        Collaboration.configure({ document: ydoc }),
        CollaborationCursor.configure({
          provider: provider,
          user: { name: this.username || 'Anonymous', color: null },
          render: user => {
            const colorIdentifier = hashCode(user.name).charAt(0)

            const cursor = document.createElement('span')
            cursor.classList.add('collaboration-cursor__caret')
            cursor.classList.add('border-' + colorIdentifier)

            const label = document.createElement('div')
            label.classList.add('collaboration-cursor__label')
            label.classList.add('bg-' + colorIdentifier)

            label.insertBefore(document.createTextNode(user.name), null)
            cursor.insertBefore(label, null)

            return cursor
          },
        }),
      ])
    },
    withHistory (extensions) {
      return extensions.concat([ History ])
    },
    isRemoteChange(transaction) {
      return transaction?.meta && Object.hasOwnProperty.apply(transaction.meta, ['y-sync$'])
    },
    showObservationSelectionModal(insertBeforeCurrentPosition = false) {
      this.addObservation = (attrs) => this.editor.commands.addObservation({ side: insertBeforeCurrentPosition ? -1 : 1, ...attrs })
    },
    isRequirementWithIdNotIn(node, requirementIds) {
      return node.type === 'requirement' && !requirementIds.includes(node.attrs.id)
    },
    updateContentWithRequirementIds(newIds) {
      if (this.feedbackRequirements === null) return
      const oldIds = this.currentValue.content.filter(node => node.type === 'requirement').map(node => node.attrs.id)
      if (!isEqual(sortBy(newIds), sortBy(oldIds))) {
        const missingIds = newIds.filter(id => !oldIds.includes(id))
        this.currentValue = {
          ...this.currentValue,
          content: this.currentValue.content
            // remove empty paragraphs after old requirements
            .filter((node, idx, array) => idx === 0 || !isEqual(node, this.getEmptyParagraph()) || !this.isRequirementWithIdNotIn(array[idx - 1], newIds))
            // remove old requirements
            .filter(node => !this.isRequirementWithIdNotIn(node, newIds))
            // add new requirements with paragraph after them
            .concat(missingIds.flatMap(id => [{ type: 'requirement', attrs: { id: id, status_id: this.defaultRequirementStatusId, comment: '' } }, this.getEmptyParagraph()]))
        }
        this.setEditorContent(this.currentValue)
        this.$emit('input', this.currentValue)
        this.$emit('localinput', this.currentValue)
      }
    },
    setEditorContent(content = {}) {
      this.editor.chain()
        .allowChangingRequirements()
        .setContent(content)
        .run()
    },
    focus(position = 0) {
      this.editor.commands.focus(position)
    },
    setOffline() {
      this.offline = true
    },
    setOnline() {
      this.offline = false
    },
  },
  watch: {
    feedbackRequirements(newIds) {
      this.updateContentWithRequirementIds(newIds)
    }
  },
  provide() {
    return {
      observations: this.observations,
      requirements: this.requirements,
      categories: this.categories,
      authors: this.authors,
      blocks: this.blocks,
      requirementStatuses: this.requirementStatuses,
      courseId: this.courseId,
      feedbackDataId: this.feedbackDataId,
      showObservationSelectionModal: this.showObservationSelectionModal
    }
  },
  mounted() {
    window.addEventListener('offline', this.setOffline)
    window.addEventListener('online', this.setOnline)

    this.updateContentWithRequirementIds(this.feedbackRequirements)

    // Two ticks after mounted, the HTML is rendered correctly
    this.$nextTick(() => {
      this.$nextTick(() => {
        this.$emit('content-ready')
      })
    })
  },
  unmounted() {
    window.removeEventListener('offline', this.setOffline)
    window.removeEventListener('online', this.setOnline)
  },
  beforeDestroy() {
    this.editor.destroy()
  },
}

const hashCode = function (string) {
  var hash = 0, i, chr
  if (string.length === 0) return hash
  for (i = 0; i < string.length; i++) {
    chr = string.charCodeAt(i)
    hash = ((hash << 5) - hash) + chr
    hash |= 0 // Convert to 32bit integer
  }
  return Math.abs(hash).toString(16)
}
</script>

