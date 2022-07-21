import {GapCursor} from 'prosemirror-gapcursor'
import {Extension} from '@tiptap/core'

/**
 * Workaround for https://github.com/ueberdosis/tiptap/issues/1899
 */
const GapCursorFocus = Extension.create({
  onFocus({ editor }) {
    editor.commands.focusWithGapcursor()
  },

  addCommands() {
    return {
      focusWithGapcursor: () => ({ editor, tr }) => {
        const { from, to } = editor.state.selection
        if (from !== to) {
          return true
        }
        const resolvedPos = editor.state.doc.resolve(from)
        if (GapCursor.valid(resolvedPos)) {
          tr.setSelection(new GapCursor(resolvedPos))
        }
      }
    }
  }
})

export default GapCursorFocus
