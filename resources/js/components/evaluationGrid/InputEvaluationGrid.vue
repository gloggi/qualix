<template>
  <div>
    <template v-for="(rowTemplate, index) in rowTemplates">
      <input-evaluation-grid-row
        :name="`${name}[${index}]`"
        :index="index"
        :row-template="rowTemplate"
        :notes-length-limit="notesLengthLimit"
        v-model="currentValue[index]"></input-evaluation-grid-row>
    </template>
  </div>
</template>

<script>
import Input from '../../mixins/input';
import InputEvaluationGridRow from './InputEvaluationGridRow.vue';

export default {
  name: 'InputEvaluationGrid',
  components: { InputEvaluationGridRow },
  mixins: [ Input ],
  props: {
    value: { type: Array, default: () => ([]) },
    rowTemplates: { type: Array, required: true },
    notesLengthLimit: { type: Number, required: false },
  },
}
</script>

<style scoped>

</style>
