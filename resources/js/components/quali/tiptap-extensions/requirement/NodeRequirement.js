import {Node, Plugin} from 'tiptap'
import { isEqual } from 'lodash'
import ElementRequirement from "./ElementRequirement"

export default class NodeRequirement extends Node {

  constructor(readonly) {
    super();
    this.readonly = readonly;
  }

  get name() {
    return 'requirement'
  }

  get schema() {
    return {
      // here you have to specify all values that can be stored in this node
      attrs: {
        id: { default: null },
        passed: { default: null },
      },
      group: 'block',
      selectable: !this.readonly,
      draggable: !this.readonly,
      // parseDOM and toDOM is still required to make copy and paste work
      parseDOM: [{
        tag: 'element-requirement',
        getAttrs: dom => ({
          id: dom.getAttribute('data-id'),
          passed: dom.getAttribute('data-passed'),
        }),
      }],
      toDOM: node => ['element-requirement', {
        'data-id': node.attrs.id,
        'data-passed': node.attrs.passed,
      }],
    }
  }

  commands({ type }) {
    return attrs => (state, dispatch) => {
      const { selection } = state;
      const position = selection.$cursor ? selection.$cursor.pos : selection.$to.pos;
      const node = type.create(attrs);
      node.attrs.id = attrs.id
      node.attrs.passed = attrs.passed
      const transaction = state.tr.insert(position, node);
      dispatch(transaction);
    };
  }

  requirementsInNode(node) {
    const requirements = []
    node.descendants(child => {
      if (child.type.name === 'requirement') requirements.push(child.attrs.id)
    })
    return requirements
  }

  get plugins() {
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
            this.requirementsInNode(state.doc.content).sort(),
            this.requirementsInNode(newState.doc.content).sort()
          )
        }
      }),
    ]
  }

  get view() {
    return ElementRequirement;
  }

}
