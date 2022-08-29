<template>
  <span>
    <b-button variant="link" href="#" @click="toggleOpen" class="text-secondary px-0">
        <slot name="question"></slot>{{ $t(trans + '.question', params) }} <i class="fas fa-circle-question"></i>
    </b-button>

    <b-collapse :id="id | kebabCase" v-model="visible" class="text-secondary">
        <slot>{{ $t(trans + '.answer', params) }}</slot>
    </b-collapse>
</span>
</template>

<script>

export default {
  name: 'HelpText',
  props: {
    id: { type: String },
    trans: { type: String, required: true },
    params: { type: Object, default: () => {} }
  },
  data() {
    return {
      visible: false
    }
  },
  methods: {
    toggleOpen() {
      this.visible = !this.visible
    }
  }
}
</script>

<style scoped>

</style>
