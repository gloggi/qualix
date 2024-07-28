<template>
  <div>
    <template v-for="(rowTemplate, index) in rowTemplates">
      <row-text v-if="rowTemplate.control_type === 'heading'">
        <h5>{{ rowTemplate.criterion }}</h5>
      </row-text>
      <input-evaluation-grid-row
        v-else
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
import RowText from '../form/RowText.vue';

export default {
  name: 'InputEvaluationGrid',
  components: { RowText, InputEvaluationGridRow },
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
