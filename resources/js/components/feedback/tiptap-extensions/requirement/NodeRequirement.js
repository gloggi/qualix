import {mergeAttributes, Node} from '@tiptap/core'
import {VueNodeViewRenderer} from '@tiptap/vue-2'
import {Plugin} from 'prosemirror-state'
import isEqual from 'lodash/isEqual'
import ElementRequirement from './ElementRequirement.vue'

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
      comment: {
        default: '',
        parseHTML: element => element.getAttribute('data-comment'),
        renderHTML: attributes => ({'data-comment': attributes.comment})
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
      },
      updateRequirementNode: ({ requirementId, requirementStatusId, comment }) => ({ tr, state }) => {
        state.doc.descendants((node, pos)  => {
          if ('requirement' !== node.type.name) return false
          if (String(requirementId) !== String(node.attrs.id)) return false

          tr.setNodeMarkup(pos, undefined, {
            ...node.attrs,
            status_id: requirementStatusId,
            comment: comment,
          })
        })
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
