<template>
  <div class="editor">
    <div v-if="errorMessage" class="invalid-feedback d-block" role="alert">
      <strong>{{ errorMessage }}</strong>
    </div>
    <editor-floating-menu v-if="!readonly" :editor="editor" v-slot="{ commands, menu }">
      <floating-menu :available-observations="availableObservations" :readonly="readonly" :commands="commands" :menu="menu"/>
    </editor-floating-menu>
    <editor-content class="editor-content" :class="{ readonly }" :editor="editor" />
    <input-hidden v-if="name" :value="formValue" :name="name"></input-hidden>
  </div>
</template>

<script>
  import {get} from 'lodash'
  import Input from '../../mixins/input'
  import {Editor, EditorContent, EditorFloatingMenu} from 'tiptap'
  import {History} from 'tiptap-extensions'
  import Observation from './tiptap-extensions/observation/NodeObservation'
  import Requirement from './tiptap-extensions/requirement/NodeRequirement'
  import InputHidden from "../form/InputHidden"
  import FloatingMenu from "./FloatingMenu"

  export default {
    name: 'QualiEditor',
    components: {FloatingMenu, InputHidden, EditorContent, EditorFloatingMenu},
    mixins: [Input],
    props: {
      value: { type: Object, default: null },
      observations: { type: Array, required: true },
      requirements: { type: Array, required: true },
      readonly: { type: Boolean, default: false },
      autofocus: { type: Boolean, default: false },
    },
    data() {
      const currentValue = JSON.parse(get(window.oldInput, this.name, 'null')) ?? this.value
      return {
        editor: new Editor({
          content: currentValue,
          editable: !this.readonly,
          autoFocus: this.autofocus,
          injectCSS: false,
          extensions: [
            new History(),
            new Observation(this.readonly),
            new Requirement(this.readonly),
          ],
          onUpdate: ({ getJSON }) => {
            this.currentValue = getJSON()
            this.$emit('input', this.currentValue)
          }
        }),
        currentValue: currentValue
      }
    },
    computed: {
      formValue() {
        return JSON.stringify(this.currentValue)
      },
      usedObservations() {
        return this.currentValue.content
          .filter(node => node.type === 'observation')
          .map(observation => observation.attrs.id)
      },
      availableObservations() {
        return this.observations.filter(observation => !this.usedObservations.includes(observation.id))
      }
    },
    provide() {
      return {
        observations: this.observations,
        requirements: this.requirements,
      }
    },
    mounted() {
      // Necessary in case we have oldInput that the outside world doesn't know about
      if (this.currentValue !== this.value) this.$emit('input', this.currentValue)
    },
    beforeDestroy() {
      this.editor.destroy()
    },
  }
</script>

<style scoped>

</style>
