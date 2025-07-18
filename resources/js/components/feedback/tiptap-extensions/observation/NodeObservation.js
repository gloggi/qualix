import {mergeAttributes, Node} from '@tiptap/core'
import {VueNodeViewRenderer} from '@tiptap/vue-3'
import ElementObservation from './ElementObservation.vue'

const NodeObservation = ({ readonly }) => Node.create({
  name: 'observation',
  group: 'block',
  selectable: !readonly,
  draggable: !readonly,

  defaultOptions: {
    readonly: readonly,
  },

  addAttributes() {
    return {
      id: {
        default: null,
        parseHTML: element => element.getAttribute('data-id'),
        renderHTML: attributes => ({'data-id': attributes.id})
      }
    }
  },

  parseHTML() {
    return [{tag: 'element-observation'}]
  },

  renderHTML({HTMLAttributes}) {
    return ['element-observation', mergeAttributes(HTMLAttributes)]
  },

  addNodeView() {
    return VueNodeViewRenderer(ElementObservation)
  },

  addCommands() {
    return {
      addObservation: attrs => ({ chain, tr }) => {
        // Insert before or after the current position (paragraph or heading), depending on side
        const position = attrs.side === -1 ? tr.selection.from - 1 : tr.selection.from
        return chain()
          .insertContentAt(position, { type: this.name, attrs }, {})
          .focus(position + 1)
          .run()
      }
    }
  }
})

export default NodeObservation
