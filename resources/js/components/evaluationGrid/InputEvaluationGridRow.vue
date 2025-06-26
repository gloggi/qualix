<template>
  <div class="form-group row" :class="{ 'is-invalid': errorMessage }">
    <label class="col-form-label" :class="labelClass">{{ rowTemplate.criterion }}</label>

    <div :class="inputColumnClass">
      <div class="row">
        <div class="col-12 col-lg-6" v-if="rowTemplate.control_type !== 'notes_only'">
          <input-evaluation-grid-row-control
            :name="`${name}[value]`"
            v-model="currentValue.value"
            :row-template="rowTemplate" />
        </div>
        <div class="col-12" :class="rowTemplate.control_type === 'notes_only' ? '' : 'col-lg-6'">
          <input-evaluation-grid-row-notes
            :name="`${name}[notes]`"
            v-model="currentValue.notes"
            :limit="notesLengthLimit"
            :placeholder="$t('t.models.evaluation_grid_row.notes')" /></div>
      </div>

      <span v-if="errorMessage" class="invalid-feedback" role="alert" style="display: block">
        <strong>{{ errorMessage }}</strong>
      </span>
    </div>
  </div>
</template>

<script>
import Input from '../../mixins/input.js'
import MultiSelect from '../MultiSelect.vue';
import InputHidden from '../form/InputHidden.vue';
import InputMultiSelect from '../form/InputMultiSelect.vue';
import InputTextarea from '../form/InputTextarea.vue';
import InputEvaluationGridRowNotes from './InputEvaluationGridRowNotes.vue';
export default {
  name: 'InputEvaluationGridRow',
  components: { InputEvaluationGridRowNotes },
  mixins: [ Input ],
  props: {
    value: { type: Object, default: () => ({}) },
    rowTemplate: { type: Object, required: true },
    notesLengthLimit: { type: Number, required: false },
  },
}
</script>

<style scoped>

</style>
