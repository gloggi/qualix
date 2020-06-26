<template>
  <div class="form-group row" :class="{ required }">
    <label :for="name | kebabCase" class="col-md-3 col-form-label text-md-right">{{ label }}</label>

    <div class="col-md-6">
      <multi-select
        :id="name | kebabCase"
        class="form-control-multiselect" :class="{ 'is-invalid': errorMessage }"
        :name="name"
        :required="required"
        :autofocus="autofocus"
        :v-focus="autofocus"
        v-bind="$attrs"
        v-on="$listeners"></multi-select>

      <span v-if="errorMessage" class="invalid-feedback" role="alert">
        <strong>{{ errorMessage }}</strong>
      </span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'InputMultiSelect',
  props: {
    name: { type: String, required: true },
    label: { type: String, required: true },
    errorMessage: { type: String, required: false },
    required: { type: Boolean, default: false },
    autofocus: { type: Boolean, default: false }
  }
}
</script>

<style scoped>

</style>
