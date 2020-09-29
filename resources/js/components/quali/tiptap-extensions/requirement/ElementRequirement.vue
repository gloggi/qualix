<template>
  <div class="requirement py-1 mb-2 d-flex" :class="selected ? 'selected' : ''">
    <requirement-menu :editable="editor.options.editable" :name="`requirements[${node.attrs.id}]`" :value="node.attrs.passed" @input="onChange" class="mr-2 my-auto"></requirement-menu>
    <h5 class="flex-grow-1 my-auto">{{ requirement.content | ucfirst }}</h5>
  </div>
</template>

<script>

import RequirementMenu from "./RequirementMenu"
export default {
  name: 'ElementRequirement',
  components: {RequirementMenu},
  // `node` is a Prosemirror Node Object
  // `updateAttrs` is a function to update attributes defined in `schema`
  // `view` is the ProseMirror view instance
  // `options` is an array of your extension options
  // `selected` is a boolean which is true when selected
  // `editor` is a reference to the TipTap editor instance
  // `getPos` is a function to retrieve the start position of the node
  // `decorations` is an array of decorations around the node
  props: [ 'node', 'updateAttrs', 'view', 'options', 'selected', 'editor', 'getPos', 'decorations'],
  inject: [ 'requirements' ],
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
    }
  }
}
</script>

<style scoped>

</style>
