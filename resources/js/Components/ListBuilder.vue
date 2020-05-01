<template>
  <div>
    <draggable v-model="currentValue" v-on="$listeners" handle=".handle">
      <div v-for="(element,idx) in currentValue">
        <slot :name="element.type" :value="element" :translations="translations" :remove="() => removeElement(idx)" />
      </div>
      <input v-if="name" type="hidden" :name="name" :value="JSON.stringify(currentValue)">
    </draggable>
    <div class="btn-toolbar d-flex justify-content-start my-2" role="toolbar">
      <template v-for="slot in addButtonSlots">
        <slot :name="slot" :add-element="addElement" />
      </template>
    </div>
  </div>
</template>

<script>
import Draggable from 'vuedraggable';

export default {
  name: 'ListBuilder',
  components: {
    Draggable,
  },
  props: {
    value: Array,
    oldValue: Array,
    name: String,
    translations: Object,
  },
  data: function() {
    return {
      currentValue: this.oldValue !== undefined ? this.oldValue : this.value
    }
  },
  computed: {
    addButtonSlots() {
      return Object.keys(this.$scopedSlots).filter(key => key.startsWith('add-'))
    }
  },
  methods: {
    onInput(...args) {
      this.$emit('input', ...args)
    },
    addElement(element) {
      this.currentValue.push(element)
    },
    removeElement(index) {
      this.currentValue.splice(index, 1)
    }
  }
}
</script>

<style scoped>

</style>
