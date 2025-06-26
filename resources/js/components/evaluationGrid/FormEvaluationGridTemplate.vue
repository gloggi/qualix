<template>

  <form-basic :action="action">
    <input-text
      name="name"
      :label="$t('t.models.evaluation_grid_template.name')"
      v-model="nameFormValue"
      required
      autofocus></input-text>

    <input-multi-select
      name="blocks"
      :label="$t('t.models.evaluation_grid_template.blocks')"
      v-model="blocksFormValue"
      :options="blocks"
      display-field="blockname_and_number"
      multiple required></input-multi-select>

    <input-multi-select
      name="requirements"
      :label="$t('t.models.evaluation_grid_template.requirements')"
      v-model="requirementsFormValue"
      :options="requirements"
      display-field="content"
      multiple required></input-multi-select>

    <input-evaluation-grid-template
      name="row_templates"
      :label="$t('t.models.evaluation_grid_template.rows')"
      v-model="rowTemplatesFormValue"
      :control-types="controlTypes"></input-evaluation-grid-template>

    <slot name="submit"></slot>

  </form-basic>
</template>

<script>
import FormBasic from '../FormBasic.vue';
import InputText from '../form/InputText.vue';
import InputMultiSelect from '../form/InputMultiSelect.vue';
import InputEvaluationGridTemplate from './InputEvaluationGridTemplate.vue';

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
      nameFormValue: this.evaluationGridTemplate ? this.evaluationGridTemplate.name : '',
      requirementsFormValue: this.evaluationGridTemplate ? this.evaluationGridTemplate.requirements.map(r => r.id).join() : '',
      blocksFormValue: this.evaluationGridTemplate ? this.evaluationGridTemplate.blocks.map(r => r.id).join() : '',
      rowTemplatesFormValue: this.evaluationGridTemplate ? this.evaluationGridTemplate.evaluation_grid_row_templates : [{ order: 1 }],
    }
  },
}
</script>

<style scoped>

</style>
