<template>
  <div class="form-group row">
    <label :for="name | kebabCase" class="col-md-3 col-form-label text-md-right py-0">{{ label }}</label>
    <div class="col-md-6">
      <b-form-checkbox
        type="checkbox"
        :id="name | kebabCase"
        :name="name"
        :class="{ 'is-invalid': errorMessage }"
        value="1"
        unchecked-value="0"
        :state="errorMessage ? false : null"
        v-model="currentValue"></b-form-checkbox>

      <span v-if="errorMessage" class="invalid-feedback" role="alert">
        <strong>{{ errorMessage }}</strong>
      </span>
    </div>
  </div>
</template>

<script>
import Input from "../../mixins/input";
export default {
  name: 'InputCheckbox',
  mixins: [ Input ],
  props: {
    label: { type: String, required: true },
  }
}
</script>

<style scoped>

</style>
