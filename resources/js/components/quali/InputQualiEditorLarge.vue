<template>
  <div>
    <div v-if="errorMessage" class="invalid-feedback d-block" role="alert">
      <strong>{{ errorMessage }}</strong>
    </div>
    <quali-editor
      :name="name"
      :class="{ 'form-control': !readonly, 'is-invalid': errorMessage }"
      v-model="currentValue"
      v-bind="$attrs"></quali-editor>
  </div>
</template>

<script>
import Input from '../../mixins/input'
import {EditorContent, EditorFloatingMenu} from 'tiptap'
import InputHidden from "../form/InputHidden"
import FloatingMenu from "./FloatingMenu"
import {get} from "lodash"

export default {
  name: 'InputQualiEditorLarge',
  components: {FloatingMenu, InputHidden, EditorContent, EditorFloatingMenu},
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
}
</script>

<style scoped>

</style>
