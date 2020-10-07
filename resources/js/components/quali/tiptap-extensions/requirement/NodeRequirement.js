import {Node} from 'tiptap'
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

  get view() {
    return ElementRequirement;
  }

}
