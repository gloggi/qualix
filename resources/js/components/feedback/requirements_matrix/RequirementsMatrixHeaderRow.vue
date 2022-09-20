<template>
  <b-tr>
    <th class="text-lg-center"></th>
    <th v-for="requirement in requirements" class="text-lg-center">{{ requirement ? requirement.content : '' }}</th>
  </b-tr>
</template>
<script>

export default {
  name: 'RequirementsMatrixHeaderRow',
  props: {
    requirements: { type: Array, required: true },
  },
}
</script>
