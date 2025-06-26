<template>
  <div class="d-flex flex-column">
    <textarea
      :id="name | kebabCase"
      :name="name"
      class="form-control" :class="{ 'is-invalid': errorMessage }"
      :placeholder="placeholder"
      :maxlength="limit"
      v-model="currentValue"
      v-bind="$attrs"></textarea>

    <span v-if="errorMessage" class="invalid-feedback" role="alert">
      <strong>{{ errorMessage }}</strong>
    </span>
  </div>
</template>

<script>
import Input from '../../mixins/input.js'
export default {
  name: 'InputEvaluationGridRowNotes',
  mixins: [ Input ],
  props: {
    placeholder: { type: String, required: false },
    limit: { type: Number, required: false }
  },
}
</script>

<style scoped>

</style>
