<template>
  <div class="d-flex pr-4 element-text-container">
    <textarea class="flex-grow-1 form-control" v-model="value.content" :placeholder="translations['t.views.quali_content.text_placeholder']" />
    <a class="text-danger" @click="remove"><i class="fas fa-minus-circle mx-3"></i></a>
    <div><i class="fas fa-arrows-alt mr-2 handle"></i></div>
  </div>
</template>

<script>

export default {
  name: 'ElementText',
  props: {
    value: Object,
    remove: Function,
    translations: Object,
  },
}
</script>

<style scoped>

</style>
