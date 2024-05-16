<template>
  <div>
    <form-basic :action="action">
      <b-card class="mb-4">
        <b-row>
          <b-col cols="12" md="6">
            <h2 class="h5 mb-2">{{ $t('t.views.admin.feedbacks.allocation.trainer_settings') }}</h2>
          </b-col>
          <b-col cols="12" md="6">
            <input-text
              id="defaultCapacity"
              v-model="maxCapacity"
              :label="$t('t.views.admin.feedbacks.allocation.number_of_feedbacks_per_trainer')"
              :max="participants.length"
              :min="1"
              name="defaultCapacity"
              type="number"
            />
          </b-col>
        </b-row>
        <div class="overflow-auto">
          <b-table-simple responsive small striped>
            <b-thead>
              <b-tr>
                <b-th>{{ $t('t.views.admin.feedbacks.allocation.trainer') }}</b-th>
                <b-th>{{ $t('t.views.admin.feedbacks.allocation.number_of_feedbacks_per_trainer') }}</b-th>
                <b-th>{{ $t('t.views.admin.feedbacks.allocation.nogo_header') }}</b-th>
              </b-tr>
            </b-thead>
            <b-tbody>
              <b-tr v-for="trainer in trainerPreferences" :key="trainer.id">
                <b-td>{{ trainer.name }}</b-td>
                <b-td>
                  <b-form-input
                    :id="`trainerCapacity-${trainer.id}`"
                    v-model="trainer.maxCapacity"
                    :max="parseInt(maxCapacity)"
                    :min="1"
                    :name="`trainerCapacity[${trainer.id}]`"
                    type="number"
                  />
                </b-td>
                <b-td>
                  <multi-select
                    :id="`trainerNogos-${trainer.id}`"
                    v-model="trainer.nogos"
                    :options="participants"
                    :placeholder="$t('t.views.admin.feedbacks.allocation.nogo')"
                    display-field="scout_name"
                    multiple
                    track-by="id"
                    valueField="id"
                  />
                </b-td>
              </b-tr>
            </b-tbody>
          </b-table-simple>
        </div>
      </b-card>

      <b-card class="mb-4">
        <b-row>
          <b-col cols="12" md="6">
            <h2 class="h5 mb-2">{{ $t('t.views.admin.feedbacks.allocation.participant_preferences') }}</h2>
          </b-col>
          <b-col cols="12" md="6">
            <input-text
              id="maxPreferences"
              v-model="maxPreferences"
              :label="$t('t.views.admin.feedbacks.allocation.number_of_preferences_per_participant')"
              :max="trainers.length"
              :min="1"
              name="maxPreferences"
              type="number"
            />
          </b-col>
        </b-row>

        <b-table-simple responsive small striped>
          <b-thead>
            <b-tr>
              <b-th>{{ $t('t.views.admin.feedbacks.allocation.participant') }}</b-th>
              <b-th v-for="i in parseInt(maxPreferences || 0)" :key="`header-preference-${i}`">
                {{ $t('t.views.admin.feedbacks.allocation.prio_with_index', {index: i}) }}
              </b-th>
              <b-th>{{ $t('t.views.admin.feedbacks.allocation.nogo_header') }}</b-th>
            </b-tr>
          </b-thead>
          <b-tbody>
            <b-tr v-for="participant in participantPreferences" :key="`participant-${participant.id}`">
              <b-td>{{ participant.name }}</b-td>

              <b-td v-for="i in parseInt(maxPreferences || 0)" :key="`preference-${participant.id}-${i}`">
                <multi-select
                  :id="`participantPreference-${participant.id}-${i}`"
                  v-model="participant.preferences[i - 1]"
                  :disabled="!isEnabled(participant, i - 1)"
                  :options="availableTrainers(participant, i - 1)"
                  :placeholder="$t('t.views.admin.feedbacks.allocation.trainer')"
                  :show-clear="true"
                  display-field="name"
                  track-by="id"
                />
              </b-td>

              <b-td>
                <multi-select
                  :id="`participantNogos-${participant.id}`"
                  v-model="participant.forbidden"
                  :options="trainers"
                  :placeholder="$t('t.views.admin.feedbacks.allocation.nogo')"
                  display-field="name"
                  multiple
                  track-by="id"
                />
              </b-td>
            </b-tr>
          </b-tbody>
        </b-table-simple>
      </b-card>

      <b-row class="align-items-center mb-4">
        <b-col md="8">
          <label class="form-label mb-1" for="priority-slider">Gewichtung der Priorisierung</label>
          <b-form-input
            id="priority-slider"
            v-model="defaultPriorityIndex"
            max="2"
            min="0"
            step="1"
            type="range"
          />
          <div class="d-flex justify-content-between">
            <small>Gering</small>
            <small>Mittel</small>
            <small>Stark</small>
          </div>
        </b-col>
        <b-col class="text-right" md="4">
          <button-submit :label="$t('t.views.admin.feedbacks.generate_allocation')" @click.prevent="submitForm"/>
        </b-col>
      </b-row>
    </form-basic>
    <b-card v-if="mappedAllocations.length">
      <form-basic :action="updateAssignmentAction">

        <h2 class="h5">{{ $t('t.views.admin.feedbacks.allocation.allocations') }}</h2>
        <b-table-simple responsive small striped>
          <b-thead>
            <b-tr>
              <b-th>{{ $t('t.views.admin.feedbacks.allocation.participant') }}</b-th>
              <b-th>{{ $t('t.views.admin.feedbacks.allocation.trainer') }}</b-th>
            </b-tr>
          </b-thead>
          <b-tbody>

            <b-tr v-for="(assignment, index) in mappedAllocations" :key="index">
              <b-td>{{ assignment.participantName }}</b-td>
              <b-td>
                <multi-select
                  :id="`allocation-${index}`"
                  :key="assignment.participantId"
                  v-model="assignment.trainerId"
                  :multiple="false"
                  :name="`feedbacks[${assignment.participantId}][users]`"
                  :options="trainers"
                  :show-clear="true"
                  display-field="name"
                />
              </b-td>
            </b-tr>
          </b-tbody>
        </b-table-simple>
        <button-submit :label="$t('t.views.admin.feedbacks.allocation.confirm_allocation')"></button-submit>
      </form-basic>

    </b-card>
  </div>

</template>

<script>
import MultiSelect from "../../MultiSelect";
import InputText from "../../form/InputText";
import InputMultiSelect from "../../form/InputMultiSelect.vue";
import FormBasic from "../../FormBasic.vue";

const {parseInt} = require("lodash");

export default {
  name: "FormFeedbackAllocation",
  components: {FormBasic, InputMultiSelect, InputText, MultiSelect},
  props: {
    action: {type: Array, required: true},
    updateAssignmentAction: {type: Array, required: true},
    courseId: {type: String, required: true},
    feedbacks: {type: Array, default: () => []},
    participants: {type: Array, required: true},
    trainers: {type: Array, required: true},
  },
  data() {
    return {
      maxPreferences: "3",
      maxCapacity: "10",
      trainerPreferences: this.trainers.map(trainer => ({
        id: trainer.id,
        name: trainer.name,
        maxCapacity: "10",
        nogos: ""
      })),
      participantPreferences: this.participants.map(participant => ({
        id: participant.id,
        name: participant.scout_name,
        preferences: Array.from({length: 3}, () => null),
        forbidden: ""
      })),
      mappedAllocations: [],
      defaultPriorityIndex: 2, // 0 = gering, 1 = mittel, 2 = stark
      priorityValues: [4, 6, 100],
    };
  },
  watch: {
    maxCapacity(newVal) {
      this.trainerPreferences.forEach(trainer => {
        trainer.maxCapacity = newVal;
      });
    },
    maxPreferences(newVal) {
      const numericVal = parseInt(newVal) || 0;
      this.priorityValues = [
        numericVal + 1,
        Math.max(5, numericVal * 2),
        100
      ];
    }
  },
  methods: {
    mapAllocationResults(allocationResult) {
      const participantMap = Object.fromEntries(this.participants.map(p => [p.id, p.scout_name]));
      const participantToTrainer = [];

      allocationResult.forEach(entry => {
        entry.participantIdents.forEach(participantId => {
          const participantName = participantMap[participantId] || $t('t.views.admin.feedbacks.allocation.unknown_participant', {name: participantId});
          const trainerId = entry.trainerIdent.toString()

          participantToTrainer.push({
            participantName: participantName,
            participantId: parseInt(participantId),
            trainerId: trainerId
          });
        });
      });

      return participantToTrainer;
    },
    isEnabled(participant, index) {
      if (index === 0) return true;
      return !!participant.preferences[index - 1];
    },
    availableTrainers(participant, index) {
      const selected = participant.preferences.slice(0, index).filter(Boolean).map(idAsString => parseInt(idAsString));
      const forbidden = participant.forbidden.split(',').map(idAsString => parseInt(idAsString)) || [];
      const excludeIds = [...selected, ...forbidden];
      return this.trainers.filter(trainer => !excludeIds.includes(trainer.id));
    },
    submitForm() {
      const trainerCapacities = this.trainerPreferences.map(trainer => [trainer.id, parseInt(trainer.maxCapacity)]);
      const participantWishes = this.participantPreferences.map(participant => [participant.id, ...participant.preferences.map(pref => pref || "x")]);
      const forbiddenWishes = [];
      this.trainerPreferences.forEach(trainer => {
        if (trainer.nogos && trainer.nogos.length) trainer.nogos.split(',').forEach(pid => forbiddenWishes.push([parseInt(pid), trainer.id]));
      });
      this.participantPreferences.forEach(participant => {
        if (participant.forbidden && participant.forbidden.length) participant.forbidden.split(',').forEach(tid => forbiddenWishes.push([participant.id, parseInt(tid)]));
      });

      const payload = {
        trainerCapacities,
        participantPreferences: participantWishes,
        numberOfWishes: parseInt(this.maxPreferences),
        forbiddenWishes,
        defaultPriority: this.priorityValues[this.defaultPriorityIndex],
      };

      window.axios.post(this.routeUri(...this.action), payload)
        .then(response => {
          this.mappedAllocations = this.mapAllocationResults(response.data);
        })
        .catch(error => {
          console.error('Error', error.response);
        });
    }
  }
};
</script>

<style scoped>
.overflow-auto {
  overflow-x: auto;
}

</style>
