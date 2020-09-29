<template>
  <span>
    <b-link href="#" @click="toggleCollapsed" class="text-secondary">
        {{ $t(key + '.question', params) }} <i class="fas fa-question-circle"></i>
    </b-link>

    <b-collapse :id="id | kebabCase" v-model="collapsed" class="text-secondary">
        <slot>{{ $t(key + '.answer', params) }}</slot>
    </b-collapse>
</span>
</template>

<script>

export default {
  name: 'HelpText',
  props: {
    id: { type: String },
    key: { type: String, required: true },
    params: { type: Object, default: () => {} }
  },
  data() {
    return {
      collapsed: true
    }
  },
  methods: {
    toggleCollapsed() {
      this.collapsed = !this.collapsed
    }
  }
}
</script>

<style scoped>

</style>
