<template>
  <div class="card">
    <div class="card-header d-flex py-1 pr-2">
      <quali-requirement-menu v-model="value" class="mr-2 my-auto"></quali-requirement-menu>
      <div class="flex-grow-1 my-auto">{{ value.content }}</div>
      <i class="fas fa-arrows-alt mx-2 handle my-auto"></i>
    </div>
    <div class="card-body">
      <slot :value="value" :translations="translations"></slot>
    </div>
  </div>
</template>

<script>

import InlineRadioButton from "./QualiRequirementMenu";
export default {
  name: 'ElementRequirement',
  components: {InlineRadioButton},
  props: {
    value: Object,
    translations: Object,
  },
}
</script>

<style scoped>

</style>
