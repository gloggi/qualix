<template>
  <div class="editor">
    <div v-if="errorMessage" class="invalid-feedback d-block" role="alert">
      <strong>{{ errorMessage }}</strong>
    </div>
    <editor-floating-menu v-if="!readonly" :editor="editor" v-slot="{ commands, isActive, menu }">
      <div
        class="editor__floating-menu"
        :class="{ 'is-active': menu.isActive }"
        :style="`top: ${menu.top}px`">

        <button-add v-if="availableObservations.length" @click="addObservation = commands.observation">{{ $t('t.models.observation.one') }}</button-add>

        <modal-add-observation :observations="availableObservations" v-model="addObservation"></modal-add-observation>

      </div>
    </editor-floating-menu>
    <editor-content class="editor-content" :class="{ readonly }" :editor="editor" />
    <input-hidden v-if="name" :value="formValue" :name="name"></input-hidden>
  </div>
</template>

<script>
  import { get } from 'lodash'
  import Input from '../../mixins/input'
  import RequirementProgress from "./RequirementProgress"
  import {Editor, EditorContent, EditorFloatingMenu} from 'tiptap'
  import {History} from 'tiptap-extensions'
  import Observation from './tiptap-extensions/observation/NodeObservation'
  import Requirement from './tiptap-extensions/requirement/NodeRequirement'
  import ModalAddObservation from "./tiptap-extensions/observation/ModalAddObservation"
  import InputHidden from "../form/InputHidden"

  export default {
    name: 'QualiEditor',
    components: {InputHidden, ModalAddObservation, RequirementProgress, EditorContent, EditorFloatingMenu},
    mixins: [Input],
    props: {
      value: { type: Object, default: null },
      observations: { type: Array, required: true },
      requirements: { type: Array, required: true },
      readonly: { type: Boolean, default: false },
    },
    data() {
      const currentValue = JSON.parse(get(window.oldInput, this.name, 'null')) ?? this.value
      return {
        addObservation: null,
        editor: new Editor({
          content: currentValue,
          editable: !this.readonly,
          autoFocus: true,
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
      addingObservation: {
        get() {
          return !!this.addObservation
        },
        set(newValue) {
          if(newValue === false) {
            this.addObservation = null
          }
        }
      },
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

<style lang="scss">
  .editor {
    position: relative;
    &__floating-menu {
      position: absolute;
      right: 0;
      z-index: 1;
      margin-top: -0.25rem;
      visibility: hidden;
      opacity: 0;
      transition: opacity 0.2s, visibility 0.2s;
      &.is-active {
        opacity: 1;
        visibility: visible;
      }
    }
  }
</style>
