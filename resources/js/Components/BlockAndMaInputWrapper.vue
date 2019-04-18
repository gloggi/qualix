<template>
  <div>
    <slot :maValue="maValue" :onBlockUpdate="onBlockUpdate"></slot>
  </div>
</template>

<script>

export default {
  name: 'BlockAndMaInputWrapper',
  data: function () {
    return {
      maValue: ''
    }
  },
  methods: {
    onBlockUpdate (blockObject) {
      this.maValue = blockObject.data
    }
  }
}
</script>

<style scoped>

</style>
