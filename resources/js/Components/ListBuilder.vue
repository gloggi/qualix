<template>
  <draggable v-model="currentValue" v-on="$listeners" handle=".handle">
    <div v-for="(element,idx) in currentValue" :key="idx">
      <template v-if="$scopedSlots[element.type]">
        <slot :name="element.type" :value="element" :translations="translations" />
      </template>
    </div>
    <input v-if="name" type="hidden" :name="name" :value="JSON.stringify(currentValue)">
  </draggable>
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
    onInput: function (...args) {
      this.$emit('input', ...args)
    }
  }
}
</script>

<style scoped>

</style>
