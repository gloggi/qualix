<template>
  <div class="form-group row required" :class="{ 'is-invalid': errorMessage }">
    <label class="col-form-label" :class="labelClass">{{ label }}</label>

    <div :class="inputColumnClass">
      <template v-for="(row, index) in currentValue">
        <input-evaluation-grid-row-template
          :name="`${name}[${index}]`"
          :index="index"
          v-model="currentValue[index]"
          :control-types="controlTypes"
          @remove="removeRow"></input-evaluation-grid-row-template>
      </template>

      <b-btn
        class="px-0"
        variant="link"
        @click="currentValue.push({})"><i class="fas fa-plus mr-1"></i> {{ $t('t.models.evaluation_grid_template.add_row') }}</b-btn>

      <span v-if="errorMessage" class="invalid-feedback" role="alert" style="display: block">
        <strong>{{ errorMessage }}</strong>
      </span>
    </div>
  </div>
</template>

<script>
import Input from '../../mixins/input'
import InputEvaluationGridRowTemplate from './InputEvaluationGridRowTemplate.vue';
export default {
  name: 'InputEvaluationGridTemplate',
  components: { InputEvaluationGridRowTemplate },
  mixins: [ Input ],
  props: {
    label: { type: String, required: true },
    value: { type: Array, default: () => ([]) },
    controlTypes: { type: Array, default: () => [] },
  },
  methods: {
    removeRow(index) {
      this.currentValue = this.currentValue.filter((row, idx) => idx !== index)
    }
  }
}
</script>

<style scoped>

</style>
