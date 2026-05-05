<template>
  <div ref="menu" class="editor__floating-menu">
    <button-add @click="editor.chain().focus().toggleHeading({ level: 3 }).run()">{{ $t('t.global.editor.heading_large') }}</button-add>
    <button-add @click="editor.chain().focus().toggleHeading({ level: 5 }).run()">{{ $t('t.global.editor.heading_normal') }}</button-add>
    <button-add @click="editor.chain().focus().toggleHeading({ level: 6 }).run()">{{ $t('t.global.editor.heading_small') }}</button-add>
    <button-add v-if="observations.length" @click="$emit('addObservation')">{{ $t('t.models.observation.one') }}</button-add>
  </div>
</template>

<script>
import ModalAddObservation from './tiptap-extensions/observation/ModalAddObservation.vue'
import ButtonAdd from './ButtonAdd.vue'

export default {
  name: 'FloatingMenu',
  components: {ModalAddObservation, ButtonAdd},
  props: {
    observations: { type: Array, default: [] },
    editor: { type: Object, required: true },
  },
  emits: ['addObservation'],
}
</script>

<style scoped>

</style>
