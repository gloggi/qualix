import {Node, TextSelection} from 'tiptap'
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
      const node = type.create(attrs);
      node.attrs.id = attrs.id

      const insertion = state.tr.replaceSelectionWith(node);

      const previousCursorPosition = state.selection.$cursor ? state.selection.$cursor.pos : state.selection.$head.pos;
      const cursorPosition = insertion.mapping.map(previousCursorPosition);
      const newSelection = TextSelection.create(insertion.doc, cursorPosition, cursorPosition);
      const insertionWithCursorPosition = insertion.setSelection(newSelection);

      dispatch(insertionWithCursorPosition);
    };
  }

  get view() {
    return ElementObservation;
  }

}
