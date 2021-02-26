<template>
  <div class="editor" :class="{ 'focus': focused }">
    <editor-floating-menu v-if="!readonly" :editor="editor" v-slot="{ commands, menu }">
      <floating-menu :observations="observations" :commands="commands" :menu="menu" @addObservation="showObservationSelectionModal"/>
    </editor-floating-menu>
    <editor-content class="editor-content" :class="{ readonly }" :editor="editor" />
    <modal-add-observation v-if="observations.length" :observations="observations" v-model="addObservation" :return-focus="{ $el: { focus: () => focus(editor.state.selection.$head.pos) } }" :show-requirements="showRequirements" :show-categories="showCategories" :show-impression="showImpression"></modal-add-observation>
    <input-hidden v-if="name" :value="formValue" :name="name"></input-hidden>
  </div>
</template>

<script>
import {sortBy, isEqual, cloneDeep} from 'lodash'
import {Editor, EditorContent, EditorFloatingMenu, TextSelection} from 'tiptap'
import {History, Heading} from 'tiptap-extensions'
import {GapCursor, gapCursor} from 'prosemirror-gapcursor'
import Observation from './tiptap-extensions/observation/NodeObservation'
import Requirement from './tiptap-extensions/requirement/NodeRequirement'
import InputHidden from "../form/InputHidden"
import FloatingMenu from "./FloatingMenu"

export default {
  name: 'QualiEditor',
  components: {FloatingMenu, InputHidden, EditorContent, EditorFloatingMenu},
  props: {
    name: { type: String },
    courseId: { type: String },
    value: { type: Object, default: null },
    observations: { type: Array, default: () => [] },
    requirements: { type: Array, required: true },
    qualiRequirements: { type: Array, default: null },
    categories: { type: Array, default: () => [] },
    readonly: { type: Boolean, default: false },
    autofocus: { type: Boolean, default: false },
    showRequirements: { type: Boolean, default: false },
    showCategories: { type: Boolean, default: false },
    showImpression: { type: Boolean, default: false },
  },
  data() {
    const editor = new Editor({
      content: this.value ?? null,
      editable: !this.readonly,
      injectCSS: false,
      // We manually add gapCursor, in order to customize it
      enableGapCursor: false,
      extensions: [
        new History(),
        new Heading({ levels: [ 3, 5, 6 ] }),
        new Observation(this.readonly),
        new Requirement(this.readonly),
      ],
      onBlur: () => {
        this.focused = false
      },
      onFocus: () => {
        this.focused = true
      },
      onUpdate: ({ getJSON }) => {
        this.currentValue = getJSON()
        this.$emit('input', this.currentValue)
      },
    })

    const gapCursorPlugin = gapCursor()
    // Patch the handleClick function so we can call it ourselves when autofocusing
    gapCursorPlugin.props.handleClick = (view, pos, event) => {
      if (!view.editable) return false
      let $pos = view.state.doc.resolve(pos)
      if (!GapCursor.valid($pos)) return false
      const calculatedPos = view.posAtCoords({left: event.clientX, top: event.clientY})
      if (calculatedPos && calculatedPos.inside > -1 && NodeSelection.isSelectable(view.state.doc.nodeAt(calculatedPos.inside))) return false
      view.dispatch(view.state.tr.setSelection(new GapCursor($pos)))
      return true
    }
    editor.registerPlugin(gapCursorPlugin)

    return {
      editor: editor,
      currentValue: this.value ?? editor.options.emptyDocument,
      focused: false,
      addObservation: null,
      gapCursor: gapCursorPlugin,
    }
  },
  computed: {
    formValue() {
      return JSON.stringify(this.currentValue)
    },
    getEmptyParagraph() {
      return () => cloneDeep(this.editor.options.emptyDocument.content[0])
    }
  },
  methods: {
    showObservationSelectionModal() {
      this.addObservation = this.editor.commands.observation
    },
    isRequirementWithIdNotIn(node, requirementIds) {
      return node.type === 'requirement' && !requirementIds.includes(node.attrs.id)
    },
    updateContentWithRequirementIds(newIds) {
      if (this.qualiRequirements === null) return
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
            .concat(missingIds.flatMap(id => [{ type: 'requirement', attrs: { id: id, passed: null } }, this.getEmptyParagraph()]))
        }
        this.setEditorContent(this.currentValue)
        this.$emit('input', this.currentValue)
      }
    },
    setEditorContent(content = {}) {
      const {
        doc,
        tr
      } = this.editor.state;
      const document = this.editor.createDocument(content);
      const selection = TextSelection.create(doc, 0, doc.content.size);
      const transaction = tr.setSelection(selection).replaceSelectionWith(document, false).setMeta('allowChangingRequirements', true);
      this.editor.view.dispatch(transaction);
    },
    focus(position = 0) {
      const { view } = this.editor, { state } = view
      const selection = TextSelection.near(state.doc.resolve(position))

      // If the node at that position is not text, avoid selecting it
      const pos = (selection instanceof TextSelection) ? selection.$anchor.pos : position
      this.editor.focus(pos)

      // Activate the gapCursor if appropriate
      const coords = view.coordsAtPos(pos)
      this.gapCursor.props.handleClick(this.editor.view, pos, { clientX: -10000, clientY: -10000 })
    },
  },
  watch: {
    qualiRequirements(newIds) {
      this.updateContentWithRequirementIds(newIds)
    }
  },
  provide() {
    return {
      observations: this.observations,
      requirements: this.requirements,
      categories: this.categories,
      courseId: this.courseId,
      showObservationSelectionModal: this.showObservationSelectionModal
    }
  },
  mounted() {
    this.updateContentWithRequirementIds(this.qualiRequirements)

    // Necessary in case we have oldInput that the outside world doesn't know about
    if (this.currentValue !== this.value) this.$emit('input', this.currentValue)

    if (this.autofocus) {
      this.focus()
    }

    // Two ticks after mounted, the HTML is rendered correctly
    this.$nextTick(() => {
      this.$nextTick(() => {
        this.$emit('content-ready')
      })
    })
  },
  beforeDestroy() {
    this.editor.destroy()
  },
}
</script>

<style scoped>

</style>
