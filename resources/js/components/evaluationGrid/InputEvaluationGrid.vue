<template>
  <div>
    <template v-for="(rowTemplate) in rowTemplates">
      <row-text v-if="rowTemplate.control_type === 'heading'">
        <h5>{{ rowTemplate.criterion }}</h5>
      </row-text>
      <input-evaluation-grid-row
        v-else
        :name="`${name}[${rowTemplate.id}]`"
        :row-template="rowTemplate"
        :notes-length-limit="notesLengthLimit"
        v-model="currentValue[rowTemplate.id]"></input-evaluation-grid-row>
    </template>
  </div>
</template>

<script>
import Input from '../../mixins/input.js';
import InputEvaluationGridRow from './InputEvaluationGridRow.vue';
import RowText from '../form/RowText.vue';

export default {
  name: 'InputEvaluationGrid',
  components: { RowText, InputEvaluationGridRow },
  mixins: [ Input ],
  props: {
    modelValue: { type: Object, required: true },
    rowTemplates: { type: Array, required: true },
    notesLengthLimit: { type: Number, required: false },
  },
}
</script>

<style scoped>

</style>
