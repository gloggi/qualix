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
          <auto-save ref="autosave" trans="t.views.feedback.progress_overview" :form="form" />
        </row-text>

      </form-basic>

      <template v-if="evaluationGrids.length">
        <h6 class="font-size-larger">{{ $t('t.views.participant_details.evaluation_grids.title') }}</h6>

        <responsive-table
          :data="evaluationGrids"
          :fields="[
                    { label: $t('t.models.evaluation_grid_template.name'), value: evaluationGrid => evaluationGrid.evaluation_grid_template.name, href: evaluationGrid => routeUri('evaluationGrid.edit', {course: courseId, evaluation_grid_template: evaluationGrid.evaluation_grid_template_id, evaluation_grid: evaluationGrid.id}) },
                    { label: $t('t.models.evaluation_grid.block'), value: evaluationGrid => evaluationGrid.block.blockname_and_number },
                    { label: $t('t.models.evaluation_grid.user'), value: evaluationGrid => evaluationGrid.user.name },
                ]"
          :actions="{
                    print: evaluationGrid => ['button-print-evaluation-grid', { courseId: courseId, evaluationGridTemplateId: evaluationGrid.evaluation_grid_template_id, evaluationGridId: evaluationGrid.id }],
                    edit: evaluationGrid => routeUri('evaluationGrid.edit', {course: courseId, evaluation_grid_template: evaluationGrid.evaluation_grid_template_id, evaluation_grid: evaluationGrid.id}),
                }">
        </responsive-table>
      </template>
    </b-modal>
    <i class="fas" :class="`fa-${requirementStatus.icon}`"></i> {{ ellipsis(comment, 50) }}
  </div>
</template>

<script>
import FormBasic from '../../FormBasic'
import InputMultiSelect from '../../form/InputMultiSelect'
import AutoSave from '../../AutoSave'
import InputTextarea from '../../form/InputTextarea'
import RowText from '../../form/RowText'

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
  components: {RowText, InputTextarea, AutoSave, InputMultiSelect, FormBasic},
  props: {
    feedback: {type: Object, required: true},
    feedbackRequirement: {type: Object, required: true},
    requirementStatuses: {type: Array, required: true},
    evaluationGrids: {type: Array, default: () => []},
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
