<template>
  <div>
    <div v-if="errorMessage" class="invalid-feedback d-block" role="alert">
      <strong>{{ errorMessage }}</strong>
    </div>
    <quali-editor
      :name="name"
      :class="{ 'form-control': !readonly, 'is-invalid': errorMessage }"
      :username="username"
      :signaling-servers="signalingServers"
      v-model="currentValue"
      v-bind="$attrs"
      @localinput="$emit('localinput')"></quali-editor>
  </div>
</template>

<script>
import Input from '../../mixins/input'
import {get} from "lodash"

export default {
  name: 'InputQualiEditorLarge',
  mixins: [Input],
  props: {
    readonly: { type: Boolean, default: false },
    value: { type: Object },
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
