<template>
  <div>
    <b-modal
      :id="`requirement-matrix-cell-${feedbackRequirement.id}`"
      :title="modalTitle"
      size="lg"
      hide-footer
      @hide="save">

      <form-basic :action="['feedback.updateRequirementStatus', {course: courseId, feedback_data: feedbackDataId, feedback_requirement: feedbackRequirement.id, noFormRestoring: 1}]" ref="form">

        <input-multi-select
          v-model="statusId"
          :label="$t('t.models.feedback_requirement.status')"
          name="requirement_status"
          :options="requirementStatuses"
          valueField="id"
          displayField="name"
          required
          @update:selected="onStatusInput"
        >
          <template #option="props">
            <i :class="`text-${props.option.color} fas fa-${props.option.icon} mr-2`"></i> {{ props.option.name }}
          </template>
          <template #single-label="props">
            <i :class="`text-${props.option.color} fas fa-${props.option.icon} mr-2`"></i> {{ props.option.name }}
          </template>
        </input-multi-select>

        <row-text>
          <auto-save ref="autosave" trans="t.views.feedback.requirements_matrix" :form="form" />
        </row-text>

      </form-basic>

    </b-modal>
    <i class="fas" :class="`fa-${requirementStatus.icon}`"></i>
  </div>
</template>

<script>
import FormBasic from './FormBasic'
import InputMultiSelect from './form/InputMultiSelect'
import AutoSave from './AutoSave'

export default {
  name: 'RequirementMatrixCell',
  components: {AutoSave, InputMultiSelect, FormBasic},
  props: {
    feedbackRequirement: {type: Object, required: false},
    requirementStatuses: {type: Array, required: true},
  },
  data: function() {
    return {
      statusId: String(this.feedbackRequirement.requirement_status_id),
    }
  },
  computed: {
    requirementStatus() {
      return this.requirementStatuses.find(status => String(status.id) === String(this.statusId))
    },
    modalTitle() {
      return this.feedbackRequirement.feedback.participant.scout_name + ': ' + this.feedbackRequirement.requirement.content
    },
    courseId() {
      return this.feedbackRequirement.feedback.feedback_data.course_id
    },
    feedbackDataId() {
      return this.feedbackRequirement.feedback.feedback_data.id
    },
    form() {
      return () => this.$refs.form
    },
  },
  methods: {
    onStatusInput(...args) {
      this.$refs.autosave.autosave(...args)
      this.$emit('input', {
        status_id: this.statusId,
      })
    },
    save() {
      this.$refs.autosave.autosave()
    }
  },
  watch: {
    'feedbackRequirement.requirement_status_id': function (requirementStatusId){
      this.statusId = String(requirementStatusId)
    }
  }
}
</script>
