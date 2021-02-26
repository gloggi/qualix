<template>
  <div class="requirement d-flex" :class="selected ? 'selected' : ''">
    <requirement-status :name="`requirements[${node.attrs.id}]`" :value="node.attrs.passed" @input="onChange" class="mr-2 my-auto"></requirement-status>
    <h5 class="flex-grow-1 my-auto">{{ requirement.content | ucfirst }}</h5>
    <b-dropdown v-if="editor.options.editable" dropleft class="mr-2 requirement-menu" no-caret variant="link"><template v-slot:button-content>
        <i class="fas fa-ellipsis-v"></i>
      </template><b-dropdown-item-button @click="onChange(1)"><i class="text-success fas fa-check-circle mr-3"></i> {{ $t('t.views.quali_content.requirements.passed') }}</b-dropdown-item-button><b-dropdown-item-button @click="onChange(null)"><i class="text-primary fas fa-binoculars mr-3"></i> {{ $t('t.views.quali_content.requirements.observing') }}</b-dropdown-item-button><b-dropdown-item-button @click="onChange(0)"><i class="text-danger fas fa-times-circle mr-3"></i> {{ $t('t.views.quali_content.requirements.failed') }}</b-dropdown-item-button><b-dropdown-item-button v-if="observations.length" @click="selectObservation"><i class="text-primary fas fa-plus mr-3"></i> {{$t('t.models.observation.one')}}</b-dropdown-item-button></b-dropdown>
  </div>
</template>

<script>

import RequirementStatus from "./RequirementStatus"
export default {
  name: 'ElementRequirement',
  components: {RequirementStatus},
  // `node` is a Prosemirror Node Object
  // `updateAttrs` is a function to update attributes defined in `schema`
  // `view` is the ProseMirror view instance
  // `options` is an array of your extension options
  // `selected` is a boolean which is true when selected
  // `editor` is a reference to the TipTap editor instance
  // `getPos` is a function to retrieve the start position of the node
  // `decorations` is an array of decorations around the node
  props: [ 'node', 'updateAttrs', 'view', 'options', 'selected', 'editor', 'getPos', 'decorations'],
  inject: [ 'requirements', 'showObservationSelectionModal', 'observations', 'courseId' ],
  computed: {
    requirement() {
      const requirement = this.requirements.find(requirement => requirement.id === this.node.attrs.id)
      if (!requirement) {
        this.remove()
      }
      return requirement || {}
    },
  },
  filters: {
    ucfirst(text) {
      if (!text || typeof text !== 'string') return text
      return text.charAt(0).toUpperCase() + text.slice(1)
    }
  },
  methods: {
    remove() {
      const tr = this.view.state.tr
      const pos = this.getPos()
      tr.delete(pos, pos + this.node.nodeSize)
      this.view.dispatch(tr)
    },
    onChange(passed) {
      this.updateAttrs({ passed: passed })
    },
    setObservationFilter() {
      if (!this.courseId) return
      let storage = JSON.parse(localStorage.courses ?? '{}')
      if (!storage) storage = {}
      if (!storage[this.courseId]) storage[this.courseId] = {}
      storage[this.courseId].selectedRequirement = this.node.attrs.id;
      localStorage.courses = JSON.stringify(storage)
    },
    setCursorAfterRequirement() {
      const pos = this.getPos() + this.node.nodeSize
      this.editor.focus(pos)
    },
    selectObservation() {
      this.setObservationFilter()
      this.setCursorAfterRequirement()
      this.showObservationSelectionModal()
    },
  }
}
</script>

<style scoped>

</style>
