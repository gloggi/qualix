<template>
  <div>
    <draggable v-model="currentValue" v-on="$listeners" handle=".handle">
      <div v-for="(element,idx) in currentValue">
        <slot :name="element.type" :value="element" :translations="translations" :remove="() => removeElement(idx)" />
      </div>
      <input v-if="name" type="hidden" :name="name" :value="JSON.stringify(currentValue)">
    </draggable>
    <b-btn-toolbar class="d-flex justify-content-start my-2">
      <slot name="add-buttons" :add-element="addElement" />
    </b-btn-toolbar>
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
  .btn-toolbar >>> > * + * {
    margin-left: 0.5rem;
  }
</style>
