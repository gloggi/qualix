<template>
  <form :method="formMethod" :action="routeUri(...arrayAction)" :enctype="enctype">
    <input-hidden v-if="methodIsNotGet" name="_method" :value="method"></input-hidden>
    <input-hidden v-if="methodIsNotGet" name="_token" :value="csrfToken"></input-hidden>
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
    methodIsNotGet() {
      return this.method !== 'GET'
    },
    formMethod() {
      return this.methodIsNotGet ? 'POST' : 'GET'
    },
  }
}
</script>

<style scoped>

</style>
