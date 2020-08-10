<template>
  <div class="form-group row" :class="{ required }">
    <label :for="name | kebabCase" class="col-md-3 col-form-label text-md-right">{{ label }}</label>

    <div class="col-md-6">
      <input
        type="file"
        :id="name | kebabCase"
        :name="name"
        :accept="accept"
        class="form-control" :class="{ 'is-invalid': errorMessage }"
        :required="required"
        :autofocus="autofocus"
        :v-focus="autofocus">

      <span v-if="errorMessage" class="invalid-feedback" role="alert">
        <strong>{{ errorMessage }}</strong>
      </span>
    </div>
  </div>
</template>

<script>
import Input from '../../mixins/input'
export default {
  name: 'InputFile',
  mixins: [ Input ],
  props: {
    accept: { type: String, required: true },
    required: { type: Boolean, default: false },
    label: { type: String, required: true },
    autofocus: { type: Boolean, default: false },
  },
}
</script>

<style scoped>

</style>
