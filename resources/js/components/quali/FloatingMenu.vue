<template>
  <div ref="menu" class="editor__floating-menu" :class="{ 'is-active': menu.isActive }">
    <button-add @click="commands.heading({ level: 5 })">{{ $t('t.global.heading') }}</button-add>
    <button-add v-if="observations.length" @click="addObservation = commands.observation">{{ $t('t.models.observation.one') }}</button-add>
    <modal-add-observation v-if="observations.length" :observations="observations" v-model="addObservation"></modal-add-observation>
  </div>
</template>

<script>
  import ModalAddObservation from "./tiptap-extensions/observation/ModalAddObservation"
  import ButtonAdd from "./ButtonAdd"

  export default {
  name: 'floating-menu',
  components: {ModalAddObservation, ButtonAdd},
  props: {
    observations: { type: Array, default: [] },
    readonly: { type: Boolean, default: false },
    commands: { type: Object, required: true },
    menu: { type: Object, required: true },
  },
  data() {
    return {
      addObservation: null,
    }
  },
  computed: {
    addingObservation: {
      get() {
        return !!this.addObservation
      },
      set(newValue) {
        if(newValue === false) {
          this.addObservation = null
        }
      }
    },
  },
  watch: {
    'menu.top': function(val) {
      // Work around CSP inline style limitation, by applying the dynamic CSS here in code instead of
      // a dynamic inline style attribute
      this.$refs.menu.style.top = val + 'px'
    }
  }
}
</script>

<style scoped>

</style>
