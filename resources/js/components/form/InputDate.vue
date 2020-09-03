<template>
  <div class="form-group row" :class="{ required }">
    <label :for="name | kebabCase" class="col-md-3 col-form-label text-md-right">{{ label }}</label>

    <div class="col-md-6">
      <b-form-datepicker
        :id="name | kebabCase"
        :name="name"
        class="form-control" :class="{ 'is-invalid': errorMessage }"
        :value="value"
        :required="required"
        :autofocus="autofocus"
        :v-focus="autofocus"
        :locale="$i18n.locale"
        :date-format-options="{ year: 'numeric', month: '2-digit', day: '2-digit' }"
        hide-header
        label-help=""></b-form-datepicker>

      <span v-if="errorMessage" class="invalid-feedback" role="alert">
        <strong>{{ errorMessage }}</strong>
      </span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'InputDate',
  props: {
    name: { type: String, required: true },
    required: { type: Boolean, default: false },
    label: { type: String, required: true },
    value: { type: String, default: '' },
    errorMessage: { type: String, required: false },
    autofocus: { type: Boolean, default: false },
  },
}
</script>

<style scoped>

</style>
