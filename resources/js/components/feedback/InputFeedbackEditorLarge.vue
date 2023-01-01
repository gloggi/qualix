<template>
  <div>
    <div v-if="errorMessage" class="invalid-feedback d-block" role="alert">
      <strong>{{ errorMessage }}</strong>
    </div>
    <feedback-editor
      :name="name"
      :class="{ 'form-control': !readonly, 'is-invalid': errorMessage || markInvalid }"
      :username="username"
      v-model="currentValue"
      v-bind="$attrs"
      @localinput="$emit('localinput')"></feedback-editor>
  </div>
</template>

<script>
import Input from '../../mixins/input'
import {get} from "lodash"
import FeedbackEditor from './FeedbackEditor'

export default {
  name: 'InputFeedbackEditorLarge',
  components: {FeedbackEditor},
  mixins: [Input],
  props: {
    readonly: { type: Boolean, default: false },
    value: { type: Object },
    markInvalid: { type: Boolean, default: false },
  },
  data() {
    return {
      currentValue: JSON.parse(get(window.Laravel.oldInput, this.name, 'null')) ?? this.value
    }
  },
  computed: {
    username () {
      return window.Laravel.username
    },
  }
}
</script>

<style scoped>

</style>
