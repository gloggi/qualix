<template>
  <div class="card">
    <div class="card-header d-flex pr-2">
      <div class="flex-grow-1">{{ value.content }}</div>
      <div><i class="fas fa-arrows-alt mr-2 handle"></i></div>
    </div>
    <div class="card-body bg-light p-0">
      <textarea class="form-control quali-note-content" v-model="value.content" />
    </div>
  </div>
</template>

<script>

export default {
  name: 'ElementText',
  props: {
    value: Object
  }
}
</script>

<style scoped>

</style>
