<template>
  <node-view-wrapper>
    <div  class="requirement d-flex" :class="selected ? 'selected' : ''" data-drag-handle>
      <requirement-status :value="node.attrs.status_id" @input="onChange" :statuses="requirementStatuses" class="mr-2 my-auto"></requirement-status>
      <h5 class="flex-grow-1 my-auto">{{ requirement.content | ucfirst }}</h5>
      <b-button v-if="editor.options.editable" v-b-toggle="`requirement-comment-${node.attrs.id}`" variant="link"><i class="fas fa-comment" :class="node.attrs.comment.length ? 'text-primary' : 'text-secondary'"></i></b-button>
      <b-dropdown v-if="editor.options.editable" dropleft class="mr-2 requirement-menu" no-caret variant="link">
        <template v-slot:button-content>
          <i class="fas fa-ellipsis-vertical"></i>
        </template><b-dropdown-item-button v-for="status in requirementStatuses" :key="status.id" @click="onChange(status.id)"><i :class="[`text-${status.color}`, `fa-${status.icon}`]" class="fas mr-3"></i> {{ status.name }}</b-dropdown-item-button></b-dropdown>
    </div>
    <b-collapse v-if="editor.options.editable" :id="`requirement-comment-${node.attrs.id}`">
      <div class="feedback-requirement-comment">
        <a :href="routeUri('feedback.requirementMatrix', {course: courseId, feedback_data: feedbackDataId})" class="float-right mr-2"><i class="fas fa-pen-to-square" /></a>
        <div class="mh-1em multiline" :class="{ 'text-muted': node.attrs.comment.length === 0 }">{{ node.attrs.comment || $t('t.views.feedback_content.comments_are_internal_and_will_not_be_printed') }}</div>
      </div>
    </b-collapse>
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
  inject: ['requirements', 'showObservationSelectionModal', 'observations', 'requirementStatuses', 'courseId', 'feedbackDataId'],
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
    onChange(status_id) {
      this.updateAttributes({ status_id: status_id })
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
  },
}
</script>

<style scoped>

</style>
