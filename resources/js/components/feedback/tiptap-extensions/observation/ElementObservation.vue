<template>
  <node-view-wrapper class="observation px-3 pb-1 mb-2" :class="selected ? 'selected' : ''" data-drag-handle>
    <div class="py-1 d-flex">
      <blockquote class="flex-grow-1 mb-0">
        <observation-content :observation="observation"></observation-content>
      </blockquote>
      <a v-if="editor.options.editable" class="text-danger delete-button" :title="$t('t.global.delete')"
         @click="deleteNode">
        <i class="fas fa-minus-circle"></i>
      </a>
    </div>
    <div class="mb-0 mt-1 d-flex justify-content-between">
      <small class="text-muted">{{ observation.block.name }}, {{ date }}</small>
      <small class="text-muted observation-author">{{ $t('t.views.feedback_content.observed_by', observation.user) }}</small>
    </div>
  </node-view-wrapper>
</template>

<script>
import {nodeViewProps, NodeViewWrapper} from '@tiptap/vue-2'
import ObservationContent from '../../../ObservationContent'

export default {
  name: 'ElementObservation',
  components: {ObservationContent, NodeViewWrapper},
  // `editor` is the editor instance
  // `node` is the current node
  // `decorations` is an array of decorations
  // `selected` is `true` when there is a `NodeSelection` at the current node view
  // `extension` gives access to the node extension, for example to get options
  // `getPos` is a function to get the document position of the current node
  // `updateAttributes` is a function to update attributes of the current node
  // `deleteNode` is a function to delete the current node
  props: nodeViewProps,
  inject: ['observations'],
  computed: {
    observation() {
      const observation = this.observations.find(observation => observation.pivot.id === this.node.attrs.id)
      if (!observation) {
        this.deleteNode()
      }
      return observation || {block: {}, participants: []}
    },
    date() {
      return new Date(this.observation.block.block_date).toLocaleDateString(this.$i18n.locale)
    }
  },
}
</script>

<style scoped>

</style>
