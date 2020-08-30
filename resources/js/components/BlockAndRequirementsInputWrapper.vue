<template>
  <div>
    <slot :requirementsValue="requirementsValue" :onBlockUpdate="onBlockUpdate"></slot>
  </div>
</template>

<script>

export default {
  name: 'BlockAndRequirementsInputWrapper',
  props: {
    initialRequirementsValue: { type: String, required: false }
  },
  data: function () {
    return {
      ignoreNextBlockUpdateEvent: !!this.initialRequirementsValue,
      requirementsValue: this.initialRequirementsValue
    }
  },
  methods: {
    onBlockUpdate (blockObject) {
      // If we have old input, ignore the first input event from the block selection component,
      // so we don't overwrite the old value of the requirements selection component initially.
      if (this.ignoreNextBlockUpdateEvent) {
        this.ignoreNextBlockUpdateEvent = false
        return
      }
      if (blockObject == null) {
        this.requirementsValue = ''
      } else {
        this.requirementsValue = blockObject.requirement_ids.join(',')
      }
    }
  }
}
</script>

<style scoped>

</style>
