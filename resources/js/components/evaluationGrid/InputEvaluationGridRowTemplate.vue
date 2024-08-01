<template>
  <b-card bg-variant="light">
    <b-button
      variant="link"
      class="card-remove-button text-danger"
      @click="$emit('remove', index)"
      :title="$t('t.global.remove')"
      :aria-label="$t('t.global.remove')">
      <i class="fas fa-circle-minus"></i>
    </b-button>
    <input-hidden v-if="currentValue.id" :name="`${name}[id]`" :value="String(currentValue.id)" />
    <input-textarea
      :name="`${name}[criterion]`"
      :label="$t('t.models.evaluation_grid_row_template.criterion')"
      v-model="currentValue.criterion"
      required
      narrow-form
    ></input-textarea>
    <input-multi-select
      :name="`${name}[control_type]`"
      :label="$t('t.models.evaluation_grid_row_template.control_type')"
      v-model="currentValue.control_type"
      :options="controlTypesWithTranslation"
      required
      :allow-empty="false"
      narrow-form
    ></input-multi-select>
    <input-text
      type="number"
      :name="`${name}[order]`"
      :label="$t('t.models.evaluation_grid_row_template.order')"
      v-model="currentValue.order"
      narrow-form
    ></input-text>
  </b-card>
</template>

<script>
import Input from '../../mixins/input'
import MultiSelect from '../MultiSelect.vue';
import InputHidden from '../form/InputHidden.vue';
import InputMultiSelect from '../form/InputMultiSelect.vue';
import InputTextarea from '../form/InputTextarea.vue';
export default {
  name: 'InputEvaluationGridRowTemplate',
  components: { InputTextarea, InputMultiSelect, InputHidden, MultiSelect },
  mixins: [ Input ],
  props: {
    value: { type: Object, default: () => {} },
    index: { type: Number, required: true },
    controlTypes: { type: Array, default: () => [] },
  },
  computed: {
    errorMessage() {
      return this.errors && this.errors.length ? this.errors[0] : undefined
    },
    controlTypesWithTranslation() {
      return this.controlTypes.map(controlType => ({
        id: controlType,
        label: this.$t(`t.models.evaluation_grid_row_template.control_types.${controlType}`)
      }))
    },
  },
}
</script>

<style scoped>

</style>
