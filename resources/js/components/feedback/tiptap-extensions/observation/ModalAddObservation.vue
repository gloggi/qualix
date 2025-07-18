<template>
  <b-modal
    v-model="addingObservation"
    :title="$t('t.views.feedback_content.select_observation')"
    size="xl"
    scrollable
    no-footer
    :return-focus="returnFocus">

    <observation-list
      :course-id="courseId"
      :observations="observations"
      :requirements="requirements"
      :categories="categories"
      :authors="authors"
      :blocks="blocks"
      :used-observations="usedObservations"
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
import ObservationContent from '../../../ObservationContent.vue'
import ObservationList from '../../../ObservationList.vue'

export default {
  name: 'ModalAddObservation',
  components: {ObservationList, ObservationContent},
  props: {
    value: {type: Function, required: false},
    observations: {type: Array, required: true},
    returnFocus: {type: Object, required: false},
    usedObservations: {type: Array, default: null},
    showRequirements: {type: Boolean, default: false},
    showCategories: {type: Boolean, default: false},
    showImpression: {type: Boolean, default: false},
  },
  emits: ['update:modelValue'],
  inject: ['courseId', 'requirements', 'categories', 'authors', 'blocks'],
  computed: {
    addingObservation: {
      get() {
        return !!this.value
      },
      set(newValue) {
        if (newValue === false) {
          this.$emit('update:modelValue', null)
        }
      }
    }
  },
  methods: {
    select(id) {
      this.value({id})
      this.addingObservation = false
    }
  }
}
</script>

<style scoped>

</style>
