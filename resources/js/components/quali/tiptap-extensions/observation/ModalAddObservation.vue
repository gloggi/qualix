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
      :show-requirements="showRequirements"
      :show-categories="showCategories"
      :show-impression="showImpression"
      pointer-cursor
      @clickObservation="observation => select(observation.pivot.id)"></observation-list>

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
      showRequirements: { type: Boolean, default: false },
      showCategories: { type: Boolean, default: false },
      showImpression: { type: Boolean, default: false },
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
