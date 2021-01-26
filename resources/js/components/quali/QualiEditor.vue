<template>
  <div class="editor" :class="{ 'focus': focused }">
    <editor-floating-menu v-if="!readonly" :editor="editor" v-slot="{ commands, menu }">
      <floating-menu :observations="observations" :commands="commands" :menu="menu" @addObservation="addObservation = commands.observation"/>
    </editor-floating-menu>
    <editor-content class="editor-content" :class="{ readonly }" :editor="editor" />
    <modal-add-observation v-if="observations.length" :observations="observations" v-model="addObservation"></modal-add-observation>
    <input-hidden v-if="name" :value="formValue" :name="name"></input-hidden>
  </div>
</template>

<script>
import {sortBy, isEqual, cloneDeep} from 'lodash'
import {Editor, EditorContent, EditorFloatingMenu} from 'tiptap'
import {History, Heading} from 'tiptap-extensions'
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
  },
  data() {
    const editor = new Editor({
      content: this.value ?? null,
      editable: !this.readonly,
      autoFocus: this.autofocus,
      injectCSS: false,
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
      }
    })
    return {
      editor: editor,
      currentValue: this.value ?? editor.options.emptyDocument,
      focused: false,
      addObservation: null,
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
    addObservationFromEditor() {
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
        this.editor.setContent(this.currentValue)
        this.$emit('input', this.currentValue)
      }
    },
    focus() {
      this.editor.focus(0)
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
      addObservation: this.addObservationFromEditor
    }
  },
  mounted() {
    this.updateContentWithRequirementIds(this.qualiRequirements)

    // Necessary in case we have oldInput that the outside world doesn't know about
    if (this.currentValue !== this.value) this.$emit('input', this.currentValue)

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
