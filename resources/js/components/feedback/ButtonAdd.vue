<template>
  <b-button variant="outline-primary" size="sm" v-on="$listeners"><i class="fas fa-plus mr-1"></i><slot></slot></b-button>
</template>

<script>

export default {
  name: 'ButtonAdd',
}
</script>

<style scoped>

</style>
