<template>
  <span>
    <draggable v-model="currentValue" :group="group" :handle="handle" :tag="tag" v-on="$listeners">
        <slot :list="currentValue" :on-input="onInput"></slot>
        <input v-if="name" type="hidden" :name="name" :value="JSON.stringify(currentValue)">
    </draggable>
  </span>
</template>

<script>
import Draggable from 'vuedraggable'

export default {
  name: 'DragAndDropList',
  components: {
    Draggable
  },
  props: {
    name: String,
    group: String,
    handle: String,
    oldValue: Array,
    tag: String,
    value: Array,
  },
  data: function() {
    return {
      currentValue: this.oldValue !== undefined ? this.oldValue : this.value
    }
  },
  methods: {
    onInput: function (...args) {
      this.$emit('input', ...args)
    }
  }
}
</script>

<style scoped>

</style>
