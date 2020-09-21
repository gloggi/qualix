<template>
  <b-modal
    v-model="addingObservation"
    :title="$t('t.views.quali_content.select_observation')"
    size="xl"
    scrollable
    hide-footer>
    <b-list-group>
      <b-list-group-item
        v-for="observation in observations"
        :key="observation.id"
        button
        @click="select(observation.id)">
        <observation-content :observation="observation"></observation-content>
      </b-list-group-item>
    </b-list-group>
  </b-modal>
</template>

<script>
  import ObservationContent from "../../../ObservationContent"

  export default {
    name: 'ModalAddObservation',
    components: {ObservationContent},
    props: {
      value: { type: Function, required: false },
      observations: { type: Array, required: true },
    },
    computed: {
      addingObservation: {
        get() {
          return !!this.value
        },
        set(newValue) {
          if(newValue === false) {
            this.$emit('input', null)
          }
        }
      }
    },
    methods: {
      select(id) {
        this.value({ id })
        this.addingObservation = false
      }
    }
  }
</script>

<style scoped>

</style>
