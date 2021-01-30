<template>
  <div class="observation px-3 pb-1 mb-2">
    <div class="py-1 d-flex" :class="selected ? 'selected' : ''">
      <blockquote class="flex-grow-1 mb-0">
        <observation-content :observation="observation"></observation-content>
      </blockquote>
      <a v-if="editor.options.editable !== false" class="text-danger delete-button" :title="$t('t.global.delete')" @click="remove">
        <i class="fas fa-minus-circle"></i>
      </a>
    </div>
    <div class="mb-0 mt-1 d-flex justify-content-between">
      <small class="text-muted">{{ observation.block.name }}, {{ date }}</small>
      <small class="text-muted observation-author">{{ $t('t.views.quali_content.observed_by', observation.user) }}</small>
    </div>
  </div>
</template>

<script>

  import ObservationContent from "../../../ObservationContent"

  export default {
  name: 'ElementObservation',
  components: {ObservationContent},
  // `node` is a Prosemirror Node Object
  // `updateAttrs` is a function to update attributes defined in `schema`
  // `view` is the ProseMirror view instance
  // `options` is an array of your extension options
  // `selected` is a boolean which is true when selected
  // `editor` is a reference to the TipTap editor instance
  // `getPos` is a function to retrieve the start position of the node
  // `decorations` is an array of decorations around the node
  props: [ 'node', 'updateAttrs', 'view', 'options', 'selected', 'editor', 'getPos', 'decorations'],
  inject: [ 'observations' ],
  computed: {
    observation() {
      const observation = this.observations.find(observation => observation.pivot.id === this.node.attrs.id)
      if (!observation) {
        this.remove()
      }
      return observation || { block: {}, participants: [] }
    },
    date() {
      return new Date(this.observation.block.block_date).toLocaleDateString(this.$i18n.locale)
    }
  },
  methods: {
    remove() {
      const tr = this.view.state.tr
      const pos = this.getPos()
      tr.delete(pos, pos + this.node.nodeSize)
      this.view.dispatch(tr)
    }
  }
}
</script>

<style scoped>

</style>
