<template>
  <div class="d-flex flex-column py-2">
    <div class="d-flex flex-row flex-wrap" :class="{ 'is-invalid': errorMessage }">
      <template v-if="type === 'slider'">
        <div class="d-flex flex-column w-100">
          <b-form-input type="range" min="0" max="9" :name="name" :value="currentValue || 0" />
          <div class="d-flex flex-row justify-content-between font-size-larger">
            <span>--</span>
            <span>-</span>
            <span>+</span>
            <span>++</span>
          </div>
        </div>
      </template>
      <template v-else-if="type === 'radiobuttons'">
        <b-form-radio v-for="(option, optionValue) in radioButtonOptions"
                      :key="name + '-' + optionValue"
                      :id="name + '-' + optionValue | kebabCase"
                      :name="name"
                      class="horizontal-radio"
                      size="lg"
                      :value="optionValue"
                      v-model="currentValue">{{ option }}</b-form-radio>
      </template>
      <template v-else-if="type === 'checkbox'">
        <input-hidden :id="name + '-unchecked-value' | kebabCase" :name="name" value="0"></input-hidden>
        <b-form-checkbox
          type="checkbox"
          :id="name | kebabCase"
          :name="name"
          :class="{ 'is-invalid': errorMessage }"
          value="7"
          unchecked-value="0"
          :state="errorMessage ? false : null"
          v-model="currentValue"
          size="lg"></b-form-checkbox>
      </template>
      <template v-else></template>
    </div>
    <span v-if="errorMessage" class="invalid-feedback" role="alert">
      <strong>{{ errorMessage }}</strong>
    </span>
  </div>
</template>

<script>
import Input from '../../mixins/input'
import InputHidden from '../form/InputHidden.vue';
export default {
  name: 'InputEvaluationGridRowControl',
  components: { InputHidden },
  mixins: [ Input ],
  props: {
    rowTemplate: { type: Object, required: true },
  },
  computed: {
    type() {
      return this.rowTemplate.control_type
    },
    radioButtonOptions() {
      return {
        '0': this.$t('t.models.evaluation_grid_row.radio_buttons.not_fulfilled'),
        '7': this.$t('t.models.evaluation_grid_row.radio_buttons.fulfilled'),
        '9': this.$t('t.models.evaluation_grid_row.radio_buttons.expectations_surpassed'),
      }
    },
  },
}
</script>

<style scoped>

</style>
