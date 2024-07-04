<template>

  <form-basic :action="action">
    <input-text
      name="name"
      :label="$t('t.models.evaluation_grid_template.name')"
      :value="nameFormValue"
      required
      autofocus></input-text>

    <input-multi-select
      name="requirements"
      :label="$t('t.models.evaluation_grid_template.requirements')"
      :value="requirementsFormValue"
      :options="requirements"
      display-field="content"
      multiple required></input-multi-select>

    <input-multi-select
      name="blocks"
      :label="$t('t.models.evaluation_grid_template.blocks')"
      :value="blocksFormValue"
      :options="blocks"
      display-field="blockname_and_number"
      multiple required></input-multi-select>

    <input-evaluation-grid-template
      name="row_templates"
      :label="$t('t.models.evaluation_grid_template.rows')"
      :value="rowTemplates"
      :control-types="controlTypes"></input-evaluation-grid-template>

    <slot name="submit"></slot>

  </form-basic>
</template>

<script>
import FormBasic from '../FormBasic';
import InputText from '../form/InputText';
import InputMultiSelect from '../form/InputMultiSelect';
import InputEvaluationGridTemplate from './InputEvaluationGridTemplate.vue';
import { get } from 'lodash';

export default {
  name: 'FormEvaluationGridTemplate',
  components: { InputEvaluationGridTemplate, InputMultiSelect, InputText, FormBasic },
  props: {
    action: { type: Array, required: true },
    courseId: { type: String, required: true },
    name: { type: String, required: false },
    requirements: { type: Array, default: () => [] },
    blocks: { type: Array, default: () => [] },
    controlTypes: { type: Array, default: () => [] },
    evaluationGridTemplate: { type: Object, default: null },
  },
  data() {
    return {
      nameFormValue: get(Laravel.oldInput, 'name', this.evaluationGridTemplate ? this.evaluationGridTemplate.name : ''),
      requirementsFormValue: get(Laravel.oldInput, 'requirements', this.evaluationGridTemplate ? this.evaluationGridTemplate.requirements.map(r => r.id).join() : ''),
      blocksFormValue: get(Laravel.oldInput, 'blocks', this.evaluationGridTemplate ? this.evaluationGridTemplate.blocks.map(r => r.id).join() : ''),
      rowTemplates: get(Laravel.oldInput, 'row_templates', this.evaluationGridTemplate ? this.evaluationGridTemplate.evaluation_grid_row_templates : [{}]),
    }
  },
}
</script>

<style scoped>

</style>
