import {Node} from 'tiptap'
import ElementObservation from "./ElementObservation"

export default class NodeObservation extends Node {

  constructor(readonly) {
    super();
    this.readonly = readonly;
  }

  get name() {
    return 'observation'
  }

  get schema() {
    return {
      // here you have to specify all values that can be stored in this node
      attrs: {
        id: {
          default: null,
        },
      },
      group: 'block',
      selectable: !this.readonly,
      draggable: !this.readonly,
      // parseDOM and toDOM is still required to make copy and paste work
      parseDOM: [{
        tag: 'element-observation',
        getAttrs: dom => ({
          id: dom.getAttribute('data-id'),
        }),
      }],
      toDOM: node => ['element-observation', {
        'data-id': node.attrs.id
      }],
    }
  }

  commands({ type }) {
    return attrs => (state, dispatch) => {
      const { selection } = state;
      const position = selection.$cursor ? selection.$cursor.pos : selection.$to.pos;
      const node = type.create(attrs);
      node.attrs.id = attrs.id
      const transaction = state.tr.insert(position, node);
      dispatch(transaction);
    };
  }

  get view() {
    return ElementObservation;
  }

}
