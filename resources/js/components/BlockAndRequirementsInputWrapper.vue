<template>
  <div>
    <slot :requirementsValue="requirementsValue" :onBlockUpdate="onBlockUpdate"></slot>
  </div>
</template>

<script>

export default {
  name: 'BlockAndRequirementsInputWrapper',
  props: {
    initialRequirementsValue: { type: String, required: false },
    blockRequirementsMapping: { type: Array, required: false }
  },
  data: function () {
    return {
      ignoreNextBlockUpdateEvent: !!this.initialRequirementsValue,
      requirementsValue: this.initialRequirementsValue
    }
  },
  methods: {
    onBlockUpdate (blockId) {
      // If we have old input, ignore the first input event from the block selection component,
      // so we don't overwrite the old value of the requirements selection component initially.
      if (this.ignoreNextBlockUpdateEvent) {
        this.ignoreNextBlockUpdateEvent = false
        return
      }

      const block = this.blockRequirementsMapping.find(b => ('' + b.id) === blockId)
      if (blockId != null && block !== undefined) {
        this.requirementsValue = block.requirement_ids.join(',')
      }
    }
  }
}
</script>

<style scoped>

</style>
