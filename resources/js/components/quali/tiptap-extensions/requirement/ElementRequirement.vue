<template>
  <node-view-wrapper class="requirement d-flex" :class="selected ? 'selected' : ''" data-drag-handle>
    <requirement-status :name="`requirements[${node.attrs.id}]`" :value="node.attrs.passed" @input="onChange"
                        class="mr-2 my-auto"></requirement-status>
    <h5 class="flex-grow-1 my-auto">{{ requirement.content | ucfirst }}</h5>
    <b-dropdown v-if="editor.options.editable" dropleft class="mr-2 requirement-menu" no-caret variant="link">
      <template v-slot:button-content>
        <i class="fas fa-ellipsis-v"></i>
      </template><b-dropdown-item-button @click="onChange(1)"><i class="text-success fas fa-check-circle mr-3"></i> {{ $t('t.views.quali_content.requirements.passed') }}</b-dropdown-item-button><b-dropdown-item-button @click="onChange(null)"><i class="text-primary fas fa-binoculars mr-3"></i> {{ $t('t.views.quali_content.requirements.observing') }}</b-dropdown-item-button><b-dropdown-item-button @click="onChange(0)"><i class="text-danger fas fa-times-circle mr-3"></i> {{ $t('t.views.quali_content.requirements.failed') }}</b-dropdown-item-button><b-dropdown-item-button v-if="observations.length" @click="selectObservation"><i class="text-primary fas fa-plus mr-3"></i> {{$t('t.models.observation.one')}}</b-dropdown-item-button></b-dropdown>
  </node-view-wrapper>
</template>

<script>
import {nodeViewProps, NodeViewWrapper} from '@tiptap/vue-2'
import RequirementStatus from './RequirementStatus'

export default {
  name: 'ElementRequirement',
  components: {RequirementStatus, NodeViewWrapper},
  // `editor` is the editor instance
  // `node` is the current node
  // `decorations` is an array of decorations
  // `selected` is `true` when there is a `NodeSelection` at the current node view
  // `extension` gives access to the node extension, for example to get options
  // `getPos` is a function to get the document position of the current node
  // `updateAttributes` is a function to update attributes of the current node
  // `deleteNode` is a function to delete the current node
  props: nodeViewProps,
  inject: ['requirements', 'showObservationSelectionModal', 'observations', 'courseId'],
  computed: {
    requirement() {
      const requirement = this.requirements.find(requirement => requirement.id === this.node.attrs.id)
      if (!requirement) {
        this.deleteNode()
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
    onChange(passed) {
      this.updateAttributes({passed: passed})
    },
    setObservationFilter() {
      if (!this.courseId) return
      let storage = JSON.parse(localStorage.courses ?? '{}')
      if (!storage) storage = {}
      if (!storage[this.courseId]) storage[this.courseId] = {}
      storage[this.courseId].selectedRequirement = this.node.attrs.id
      localStorage.courses = JSON.stringify(storage)
    },
    setCursorAfterRequirement() {
      const pos = this.getPos() + this.node.nodeSize
      this.editor.commands.focus(pos)
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
