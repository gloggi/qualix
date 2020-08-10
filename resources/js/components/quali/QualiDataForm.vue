<template>
  <div>
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

    <slot></slot>

    <row-text>
      <b-button variant="link" class="px-0" v-b-toggle.collapse-trainer-assignments>
        {{ $t('t.views.admin.qualis.trainer_assignment') }} <i class="fas fa-caret-down"></i>
      </b-button>
    </row-text>

    <b-collapse id="collapse-trainer-assignments" v-model="trainerAssignmentsVisible">
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

  </div>
</template>

<script>

export default {
  name: 'QualiDataForm',
  props: {
    name: { type: String, required: false },
    qualis: { type: Array, default: undefined },
    participants: { type: Array, required: true },
    requirements: { type: Array, requried: true },
    trainers: { type: Array, required: true },
    hideTrainerAssignments: { type: Boolean, default: true }
  },
  data() {
    return {
      currentName: this.name,
      participantsFormValue: this.qualis ? this.qualis.map(q => q.participant.id).join() : this.participants.map(p => p.id).join(),
      requirementsFormValue: this.qualis ? [...new Set(this.qualis.flatMap(q => q.requirements.map(r => r.id)))].join() : this.requirements.map(r => r.id).join(),
      trainerAssignmentsVisible: !this.hideTrainerAssignments,
      trainerAssignments: this.participants
        .sort((a, b) => a.scout_name.localeCompare(b.scout_name))
        .map(p => {
          const quali = this.qualis ? this.qualis.find(q => q.participant.id === p.id) : undefined
          return { participantId: p.id, trainerId: '' + (quali && quali.user ? quali.user.id : null) }
        })
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
    }
  }
}
</script>

<style scoped>

</style>
