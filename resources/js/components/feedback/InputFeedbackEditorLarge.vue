<template>
  <div>
    <div v-if="errorMessage" class="invalid-feedback d-block" role="alert">
      <strong>{{ errorMessage }}</strong>
    </div>
    <feedback-editor
      :name="name"
      :class="{ 'form-control': !readonly, 'is-invalid': errorMessage || markInvalid }"
      :username="username"
      :signaling-servers="signalingServers"
      v-model="currentValue"
      v-bind="$attrs"
      @localinput="$emit('localinput')"></feedback-editor>
  </div>
</template>

<script>
import Input from '../../mixins/input'
import {get} from "lodash"

export default {
  name: 'InputFeedbackEditorLarge',
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
    signalingServers () {
      return window.Laravel.signalingServers
    },
  }
}
</script>

<style scoped>

</style>
