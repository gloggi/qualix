<template>
  <div>
    <slot :requirementsValue="requirementsValue" :onBlockUpdate="onBlockUpdate"></slot>
  </div>
</template>

<script>

export default {
  name: 'BlockAndRequirementsInputWrapper',
  props: {
    hasOldValues: {
        type: Boolean,
        default: false
    }
  },
  data: function () {
    return {
      // If we have old input, ignore the first input event from the block selection component,
      // so we don't overwrite the old value of the requirements selection component initially.
      ignoreNextBlockUpdateEvent: this.hasOldValues,
      requirementsValue: ''
    }
  },
  methods: {
    onBlockUpdate (blockObject) {
      if (this.ignoreNextBlockUpdateEvent) {
        this.ignoreNextBlockUpdateEvent = false
        return
      }
      this.requirementsValue = blockObject.data
    }
  }
}
</script>

<style scoped>

</style>
