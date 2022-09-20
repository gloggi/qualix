<template>
  <div>
    <b-modal
      :id="`requirement-matrix-cell-${feedbackRequirement.participant_id}-${feedbackRequirement.requirement_id}`"
      :title="modalTitle"
      size="lg"
      hide-footer
      @hide="save">

      <form-basic :action="['feedback.updateRequirementStatus', {course: courseId, feedback_data: feedbackDataId, participant: feedbackRequirement.participant_id, requirement: feedbackRequirement.requirement_id, noFormRestoring: 1}]" ref="form">

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

        <input-textarea
          v-model="comment"
          name="comment"
          :label="$t('t.models.feedback_requirement.comment')"
          rows="5"
          @input="onCommentInput"></input-textarea>

        <row-text>
          <auto-save ref="autosave" trans="t.views.feedback.requirements_matrix" :form="form" />
        </row-text>

      </form-basic>

    </b-modal>
    <i class="fas" :class="`fa-${requirementStatus.icon}`"></i> {{ ellipsis(comment, 50) }}
  </div>
</template>

<script>
import FormBasic from '../../FormBasic'
import InputMultiSelect from '../../form/InputMultiSelect'
import AutoSave from '../../AutoSave'
import InputTextarea from '../../form/InputTextarea'

const ellipsis = function(text, max) {
  if (text.length <= max) {
    return text;
  }
  const dots = '…';
  let i = dots.length;
  text = text.split(' ').filter(function (word) {
    i += word.length;
    if (i > max) {
      return false;
    }
    i += 1; // add a space character after a word
    return true;
  }).join(' ').replace(/(,|\n|\r\n|\.|\?|!)+$/, '');

  return text + dots;
}

export default {
  name: 'RequirementsMatrixCell',
  components: {InputTextarea, AutoSave, InputMultiSelect, FormBasic},
  props: {
    feedback: {type: Object, required: true},
    feedbackRequirement: {type: Object, required: true},
    requirementStatuses: {type: Array, required: true},
  },
  data: function() {
    return {
      statusId: String(this.feedbackRequirement.requirement_status_id),
      comment: this.feedbackRequirement.comment,
    }
  },
  computed: {
    requirementStatus() {
      return this.requirementStatuses.find(status => String(status.id) === String(this.statusId))
    },
    modalTitle() {
      return this.feedback.participant.scout_name + ': ' + this.feedbackRequirement.requirement.content
    },
    courseId() {
      return this.feedback.feedback_data.course_id
    },
    feedbackDataId() {
      return this.feedback.feedback_data.id
    },
    form() {
      return () => this.$refs.form
    },
  },
  methods: {
    onStatusInput(...args) {
      this.$refs.autosave.autosave(...args)
      this.emitUpdate()
    },
    onCommentInput(...args) {
      this.$refs.autosave.onInput(...args)
      this.emitUpdate()
    },
    emitUpdate() {
      this.$emit('input', {
        requirementId: this.feedbackRequirement.requirement_id,
        requirementStatusId: this.statusId,
        comment: this.comment,
      })
    },
    save() {
      this.$refs.autosave.autosave()
    },
    ellipsis,
  },
  watch: {
    'feedbackRequirement.requirement_status_id': function (requirementStatusId){
      this.statusId = String(requirementStatusId)
    },
    'feedbackRequirement.comment': function (comment){
      this.comment = String(comment)
    }
  }
}
</script>
