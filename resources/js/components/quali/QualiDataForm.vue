<template>
  <div>
    <input-text name="name" v-model="qualiData.name" :label="$t('t.models.quali.name')" required autofocus></input-text>

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

    <button-submit>

      <a v-if="backUrl" :href="backUrl">{{ $t('t.views.admin.qualis.go_back_to_quali_list') }}</a>

    </button-submit>
  </div>
</template>

<script>

export default {
  name: 'QualiDataForm',
  props: {
    qualiData: { type: Object, required: true },
    participants: { type: Array, required: true },
    requirements: { type: Array, requried: true },
    trainers: { type: Array, required: true },
    hideTrainerAssignments: { type: Boolean, default: true },
    backUrl: { type: String, required: false }
  },
  data() {
    return {
      participantsFormValue: this.qualiData.qualis.map(q => q.participant.id).join(),
      requirementsFormValue: [...new Set(this.qualiData.qualis.flatMap(q => q.requirements.map(r => r.id)))].join(),
      trainerAssignmentsVisible: !this.hideTrainerAssignments,
      trainerAssignments: this.participants
        .sort((a, b) => a.scout_name.localeCompare(b.scout_name))
        .map(p => {
          const quali = this.qualiData.qualis.find(q => q.participant.id === p.id)
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
