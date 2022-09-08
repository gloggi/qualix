<template>
  <form-basic :action="action">
    <input-text name="name" v-model="currentName" :label="$t('t.models.feedback.name')" required autofocus></input-text>

    <input-multi-select
      name="participants"
      v-model="participantsFormValue"
      :label="$t('t.models.feedback.participants')"
      required
      :options="participants"
      :groups="allParticipantGroups"
      display-field="scout_name"
      multiple></input-multi-select>

    <input-multi-select
      name="requirements"
      v-model="requirementsFormValue"
      :label="$t('t.models.feedback.requirements')"
      :options="requirements"
      :groups="requirementGroups"
      display-field="content"
      multiple></input-multi-select>


    <template v-if="feedbackContentsTemplate">
      <row-text>
        <b-button variant="link" class="px-0 mr-2" v-b-toggle.collapse-feedback-contents-template @click="focusFeedbackContentsTemplate">
          {{ $t('t.models.feedback.feedback_contents_template') }} <i class="fas fa-caret-down"></i>
        </b-button>
      </row-text>

      <b-collapse id="collapse-feedback-contents-template" v-model="feedbackContentsTemplateVisible">
        <row-text>
          <help-text id="feedbackContentsTemplateHelp" trans="t.views.admin.feedbacks.feedback_contents_template_description"></help-text>
        </row-text>
        <input-feedback-editor
          ref="feedbackContentsTemplate"
          name="feedback_contents_template"
          :requirements="requirements"
          :requirement-statuses="requirementStatuses"
          :selected-requirements="requirementsFormValue"
          label=""
          :course-id="courseId"
        ></input-feedback-editor>
      </b-collapse>
    </template>

    <row-text>
      <b-button ref="trainerAssignments" variant="link" class="px-0" v-b-toggle.collapse-trainer-assignments>
        {{ $t('t.views.admin.feedbacks.trainer_assignment') }} <i class="fas fa-caret-down"></i>
      </b-button>
    </row-text>

    <b-collapse id="collapse-trainer-assignments" :visible="highlightTrainerAssignments" :appear="true" @shown="doHighlightTrainerAssignments">
      <input-multi-select
        v-for="assignment in trainerAssignments"
        :key="assignment.participantId"
        v-if="displayTrainerAssignment(assignment.participantId)"
        :name="`feedbacks[${assignment.participantId}][users]`"
        v-model="assignment.trainerIds"
        :label="scoutName(assignment.participantId)"
        :options="trainers"
        display-field="name"
        multiple
        :show-clear="true"></input-multi-select>
    </b-collapse>

    <slot name="submit"></slot>

  </form-basic>
</template>

<script>
import HelpText from "../HelpText"

export default {
  name: 'FormFeedbackData',
  components: {HelpText},
  props: {
    action: {},
    courseId: { type: String, required: true },
    name: { type: String, required: false },
    feedbacks: { type: Array, default: undefined },
    participants: { type: Array, required: true },
    participantGroups: { type: Object, default: () => ({}) },
    requirements: { type: Array, default: () => [] },
    requirementStatuses: { type: Array, default: () => [] },
    trainers: { type: Array, required: true },
    highlightTrainerAssignments: { type: Boolean, default: false },
    feedbackContentsTemplate: { type: Boolean, default: false },
  },
  data() {
    return {
      currentName: this.name,
      participantsFormValue: this.feedbacks ? this.feedbacks.map(q => q.participant.id).join() : this.participants.map(p => p.id).join(),
      requirementsFormValue: this.feedbacks ? [...new Set(this.feedbacks.flatMap(q => q.requirements.map(r => r.id)))].join() : this.requirements.map(r => r.id).join(),
      trainerAssignments: this.participants
        .sort((a, b) => a.scout_name.localeCompare(b.scout_name))
        .map(p => {
          const feedback = this.feedbacks ? this.feedbacks.find(q => q.participant.id === p.id) : undefined
          return { participantId: p.id, trainerIds: '' + (feedback && feedback.users ? feedback.users.map(u => u.id).join(',') : '') }
        }),
      feedbackContentsTemplateVisible: false,
      shouldHighlightTrainerAssignments: this.highlightTrainerAssignments,
    }
  },
  computed: {
    allParticipantGroups() {
      return {
        ...this.participantGroups,
        [this.$t('t.views.admin.feedbacks.select_all_participants')]: this.participants.map(p => p.id).join()
      }
    },
    requirementGroups() {
      return {
        [this.$t('t.views.admin.feedbacks.select_all_requirements')]: this.requirements.map(p => p.id).join()
      }
    }
  },
  methods: {
    displayTrainerAssignment(participantId) {
      return this.participantsFormValue.split(',').includes('' + participantId)
    },
    scoutName(participantId) {
      return this.participants.find(p => '' + p.id === '' + participantId).scout_name
    },
    focusFeedbackContentsTemplate() {
      if (!this.feedbackContentsTemplateVisible) this.$refs.feedbackContentsTemplate.focus()
    },
    doHighlightTrainerAssignments() {
      if (this.shouldHighlightTrainerAssignments) {
        this.shouldHighlightTrainerAssignments = false
        this.$nextTick(() => {
          this.$refs.trainerAssignments.focus()
          this.$refs.trainerAssignments.scrollIntoView({ behavior: 'smooth' })
        })
      }
    }
  }
}
</script>

<style scoped>

</style>
