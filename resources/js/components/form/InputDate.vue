<template>
  <div class="form-group row" :class="{ required }">
    <label :for="kebabCase(name)" class="col-form-label" :class="labelClass">{{ label }}</label>

    <div :class="inputColumnClass">
      <b-form-input
        :id="kebabCase(name)"
        :name="name"
        type="date"
        class="form-control" :class="{ 'is-invalid': errorMessage }"
        v-model="currentValue"
        :required="required"
        :autofocus="autofocus"
        :v-focus="autofocus"
        :locale="$i18n.locale"
        :date-format-options="{ year: 'numeric', month: '2-digit', day: '2-digit' }"
        no-header
        label-help=""></b-form-input>

      <span v-if="errorMessage" class="invalid-feedback" role="alert">
        <strong>{{ errorMessage }}</strong>
      </span>
    </div>
  </div>
</template>

<script>
import Input from '../../mixins/input.js'
export default {
  name: 'InputDate',
  mixins: [ Input ],
  props: {
    required: { type: Boolean, default: false },
    label: { type: String, required: true },
    autofocus: { type: Boolean, default: false },
  },
}
</script>

<style scoped>

</style>
