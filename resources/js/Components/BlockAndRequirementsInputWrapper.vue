<template>
  <div>
    <slot :requirementsValue="requirementsValue" :onBlockUpdate="onBlockUpdate"></slot>
  </div>
</template>

<script>

export default {
  name: 'BlockAndRequirementsInputWrapper',
  data: function () {
    return {
      requirementsValue: ''
    }
  },
  methods: {
    onBlockUpdate (blockObject) {
      this.requirementsValue = blockObject.data
    }
  }
}
</script>

<style scoped>

</style>
