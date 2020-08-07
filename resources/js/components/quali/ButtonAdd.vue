<template>
  <button class="btn btn-outline-primary" @click="$emit('click', payload)" type="button"><i class="fas fa-plus mr-1"></i><slot></slot></button>
</template>

<script>

export default {
  name: 'ButtonAdd',
  props: {
    payload: Object,
  },
}
</script>

<style scoped>

</style>
