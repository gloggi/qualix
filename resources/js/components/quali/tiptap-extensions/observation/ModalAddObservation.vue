<template>
  <b-modal
    v-model="addingObservation"
    :title="$t('t.views.quali_content.select_observation')"
    size="xl"
    scrollable
    hide-footer>

    <observation-list
      :course-id="courseId"
      :observations="observations"
      :requirements="requirements"
      :categories="categories"
      show-content
      show-block
      show-user
      pointer-cursor
      @clickObservation="observation => select(observation.id)"></observation-list>

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
    inject: [ 'courseId', 'requirements', 'categories' ],
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
