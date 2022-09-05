<template>
  <div>
    <b-modal
      :id="`requirement-matrix-cell-${feedbackRequirement.id}`"
      :title="modalTitle"
      size="lg"
      hide-footer>

      <form-basic :action="['feedback.updateRequirementStatus', {course: courseId, feedback_data: feedbackDataId, feedback_requirement: feedbackRequirement.id}]">

        <input-multi-select
          v-model="status"
          :label="$t('t.models.feedback_requirement.status')"
          name="requirement_status"
          :options="requirementStatuses"
          valueField="id"
          displayField="name"
          required
        >
          <template #option="props">
            <i :class="`text-${props.option.color} fas fa-${props.option.icon} mr-2`"></i> {{ props.option.name }}
          </template>
          <template #single-label="props">
            <i :class="`text-${props.option.color} fas fa-${props.option.icon} mr-2`"></i> {{ props.option.name }}
          </template>
        </input-multi-select>

        <button-submit></button-submit>

      </form-basic>

    </b-modal>
    <i class="fas" :class="`fa-${requirementStatus.icon}`"></i>
  </div>
</template>

<script>
import FormBasic from './FormBasic'
import InputMultiSelect from './form/InputMultiSelect'
import ButtonSubmit from './form/ButtonSubmit'

export default {
  name: 'RequirementMatrixCell',
  components: {ButtonSubmit, InputMultiSelect, FormBasic},
  props: {
    feedbackRequirement: {type: Object, required: false},
    requirementStatuses: {type: Array, required: true},
  },
  data: function() {
    return {
      status: String(this.feedbackRequirement.requirement_status_id),
    }
  },
  computed: {
    requirementStatus() {
      const statusId = this.feedbackRequirement?.requirement_status_id
      return this.requirementStatuses.find(status => String(status.id) === String(statusId))
    },
    modalTitle() {
      return this.feedbackRequirement.feedback.participant.scout_name + ': ' + this.feedbackRequirement.requirement.content
    },
    courseId() {
      return this.feedbackRequirement.feedback.feedback_data.course_id
    },
    feedbackDataId() {
      return this.feedbackRequirement.feedback.feedback_data.id
    }
  }
}
</script>

<style scoped>

</style>
