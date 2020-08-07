<template>
  <div class="card">
    <div class="card-header d-flex p-2">
      <requirement-menu v-model="value.passed" class="mr-2 my-auto"></requirement-menu>
      <h5 class="flex-grow-1 my-auto">{{ value.content }}</h5>
      <i class="fas fa-arrows-alt mx-2 handle my-auto"></i>
    </div>
    <div class="card-body">
      <slot :value="value" :translations="translations"></slot>
    </div>
  </div>
</template>

<script>

import RequirementMenu from "./RequirementMenu";
export default {
  name: 'ElementRequirement',
  components: {RequirementMenu},
  props: {
    value: Object,
    translations: Object,
  },
}
</script>

<style scoped>

</style>
