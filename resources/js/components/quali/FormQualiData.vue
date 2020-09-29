<template>
  <form-basic :action="action">
    <input-text name="name" v-model="currentName" :label="$t('t.models.quali.name')" required autofocus></input-text>

    <input-multi-select
      name="participants"
      v-model="participantsFormValue"
      :label="$t('t.models.quali.participants')"
      required
      :options="participants"
      :groups="participantGroups"
      display-field="scout_name"
      multiple></input-multi-select>

    <input-multi-select
      name="requirements"
      v-model="requirementsFormValue"
      :label="$t('t.models.quali.requirements')"
      :options="requirements"
      :groups="requirementGroups"
      display-field="content"
      multiple></input-multi-select>


    <template v-if="qualiNotesTemplate">
      <row-text>
        <b-button variant="link" class="px-0 mr-2" v-b-toggle.collapse-quali-notes-template @click="focusQualiNotesTemplate">
          {{ $t('t.views.admin.qualis.quali_notes_template') }} <i class="fas fa-caret-down"></i>
        </b-button>
        <help-text id="qualiNotesTemplateHelp" trans="t.views.admin.qualis.quali_notes_template_description"></help-text>
      </row-text>

      <b-collapse id="collapse-quali-notes-template" v-model="qualiNotesTemplateVisible">
        <input-quali-editor
          ref="qualiNotesTemplate"
          name="quali_notes_template"
          :requirements="requirements"
          :selected-requirements="requirementsFormValue"
          label=""
        ></input-quali-editor>
      </b-collapse>
    </template>

    <row-text>
      <b-button variant="link" class="px-0" v-b-toggle.collapse-trainer-assignments>
        {{ $t('t.views.admin.qualis.trainer_assignment') }} <i class="fas fa-caret-down"></i>
      </b-button>
    </row-text>

    <b-collapse id="collapse-trainer-assignments" :visible="false">
      <input-multi-select
        v-for="assignment in trainerAssignments"
        :key="assignment.participantId"
        v-if="displayTrainerAssignment(assignment.participantId)"
        :name="`qualis[${assignment.participantId}][user]`"
        v-model="assignment.trainerId"
        :label="scoutName(assignment.participantId)"
        :options="trainers"
        display-field="name"
        :show-clear="true"></input-multi-select>
    </b-collapse>

    <slot name="submit"></slot>

  </form-basic>
</template>

<script>

import HelpText from "../HelpText"
export default {
  name: 'FormQualiData',
  components: {HelpText},
  props: {
    action: {},
    name: { type: String, required: false },
    qualis: { type: Array, default: undefined },
    participants: { type: Array, required: true },
    requirements: { type: Array, requried: true },
    trainers: { type: Array, required: true },
    qualiNotesTemplate: { type: Boolean, default: false },
  },
  data() {
    return {
      currentName: this.name,
      participantsFormValue: this.qualis ? this.qualis.map(q => q.participant.id).join() : this.participants.map(p => p.id).join(),
      requirementsFormValue: this.qualis ? [...new Set(this.qualis.flatMap(q => q.requirements.map(r => r.id)))].join() : this.requirements.map(r => r.id).join(),
      trainerAssignments: this.participants
        .sort((a, b) => a.scout_name.localeCompare(b.scout_name))
        .map(p => {
          const quali = this.qualis ? this.qualis.find(q => q.participant.id === p.id) : undefined
          return { participantId: p.id, trainerId: '' + (quali && quali.user ? quali.user.id : null) }
        }),
      qualiNotesTemplateVisible: false,
    }
  },
  computed: {
    participantGroups() {
      return {
        [this.$t('t.views.admin.qualis.select_all_participants')]: this.participants.map(p => p.id).join()
      }
    },
    requirementGroups() {
      return {
        [this.$t('t.views.admin.qualis.select_all_requirements')]: this.requirements.map(p => p.id).join()
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
    focusQualiNotesTemplate() {
      if (!this.qualiNotesTemplateVisible) this.$refs.qualiNotesTemplate.focus()
    }
  }
}
</script>

<style scoped>

</style>
