<template>
  <form :method="formMethod" :action="routeUri(...arrayAction)" :enctype="enctype">
    <input-hidden name="_method" :value="method"></input-hidden>
    <input-hidden name="_token" :value="csrfToken"></input-hidden>
    <slot></slot>
  </form>
</template>

<script>
import { isArray } from 'lodash'
import InputHidden from "./form/InputHidden"

export default {
  name: 'FormBasic',
  components: {InputHidden},
  props: {
    action: { type: [Array, String], required: true },
    enctype: { type: String, default: 'application/x-www-form-urlencoded' }
  },
  computed: {
    arrayAction() {
      return isArray(this.action) ? this.action : [this.action]
    },
    csrfToken() {
      return window.Laravel.csrf;
    },
    method() {
      return this.routeMethod(...this.arrayAction)
    },
    formMethod() {
      return (this.method && this.method === 'GET') ? 'GET' : 'POST'
    },
  }
}
</script>

<style scoped>

</style>
