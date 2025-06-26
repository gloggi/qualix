<template>
  <div></div>
</template>

<script>
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
