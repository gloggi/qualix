<template>
  <div></div>
</template>

<script>
import { isArray } from 'lodash'
import InputHidden from "./form/InputHidden"

export default {
  name: 'RefreshCsrf',
  created () {
    if (typeof window.opener.updateCsrf === 'function') {
      window.opener.updateCsrf(window.axios.defaults.headers.common['X-CSRF-TOKEN'])
    }
    window.close()
  }
}
</script>

<style scoped>

</style>
