import {mergeAttributes, Node} from '@tiptap/core'
import {VueNodeViewRenderer} from '@tiptap/vue-2'
import {Plugin} from 'prosemirror-state'
import {isEqual} from 'lodash'
import ElementRequirement from './ElementRequirement'

const NodeRequirement = ({ readonly }) => Node.create({
  name: 'requirement',
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
      },
      status_id: {
        default: null,
        parseHTML: element => element.getAttribute('data-status-id'),
        renderHTML: attributes => ({'data-status-id': attributes.status_id})
      },
    }
  },

  parseHTML() {
    return [{tag: 'element-requirement'}]
  },

  renderHTML({HTMLAttributes}) {
    return ['element-requirement', mergeAttributes(HTMLAttributes)]
  },

  addNodeView() {
    return VueNodeViewRenderer(ElementRequirement)
  },

  addProseMirrorPlugins() {
    return [
      new Plugin({
        filterTransaction: (transaction, state) => {
          // Allow programmatic change of requirements
          if (transaction.getMeta('allowChangingRequirements') === true) return true

          // Avoid endless recursion when simulating the effects of the transaction
          if (transaction.getMeta('filteringRequirementDeletion') === true) return true
          transaction.setMeta('filteringRequirementDeletion', true)

          // Simulate the transaction
          const newState = state.apply(transaction)

          // Check that the same requirements are still present in any order after the transaction
          return isEqual(
            requirementsInNode(state.doc.content).sort(),
            requirementsInNode(newState.doc.content).sort()
          )
        }
      }),
    ]
  },

  addCommands() {
    return {
      allowChangingRequirements: () => ({ tr }) => {
        tr.setMeta('allowChangingRequirements', true)
        return true
      }
    }
  }
})

const requirementsInNode = function (node) {
  const requirements = []
  node.descendants(child => {
    if (child.type.name === 'requirement') requirements.push(child.attrs.id)
  })
  return requirements
}

export default NodeRequirement
