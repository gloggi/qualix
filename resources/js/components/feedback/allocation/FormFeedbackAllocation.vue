<template>
  <div>
    <form-basic :action="action">

      <b-card :header="$t('t.views.admin.feedbacks.allocation.trainer_settings')" class="mb-4">
        <b-row
          v-for="trainer in trainerPreferences"
          :key="trainer.id"
          class="align-items-center py-2 border-bottom"
        >
          <b-col class="align-middle" cols="12" md="4">
            {{ trainer.name }}
          </b-col>
          <b-col cols="12" md="4">
            <div class="d-flex align-items-center">
              <span class="text-muted small pr-2">
                {{ $t('t.views.admin.feedbacks.allocation.number_of_feedbacks_per_trainer') }}
              </span>
              <b-form-input
                :id="`trainerCapacity-${trainer.id}`"
                v-model="trainer.maxCapacity"
                :max="participantCount"
                :min="0"
                :name="`trainerCapacity[${trainer.id}]`"
                class="w-auto"
                style="max-width: 5rem"
                type="number"
              />

            </div>
          </b-col>

          <b-col cols="12" md="4">
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
          </b-col>
        </b-row>
      </b-card>


      <b-card :header="$t('t.views.admin.feedbacks.allocation.participant_preferences')"
              class="mb-4">

        <b-row
          v-for="participant in participantPreferences"
          :key="`participant-${participant.id}`"
          class="align-items-center py-2 border-bottom"
        >
          <b-col cols="12" md="2">
            {{ participant.name }}
          </b-col>
          <b-col cols="12" md="6">
            <multi-select
              :id="`participantPreference-${participant.id}`"
              v-model="participant.preferences"
              :options="trainersWithPrio[participant.id]"
              :placeholder="$t('t.views.admin.feedbacks.allocation.wishes')"
              :show-clear="true"
              display-field="name"
              multiple
              track-by="id"
            />
          </b-col>
          <b-col cols="12" md="4">
            <multi-select
              :id="`participantNogos-${participant.id}`"
              v-model="participant.forbidden"
              :options="trainers"
              :placeholder="$t('t.views.admin.feedbacks.allocation.nogo')"
              display-field="name"
              multiple
              track-by="id"
            />
          </b-col>
        </b-row>
      </b-card>

      <b-row class="align-items-center mb-4">
        <b-col md="6">
          <label class="form-label mb-1" for="priority-slider">
            {{ $t('t.views.admin.feedbacks.allocation.prioritization_weight') }}
          </label>
          <b-form-input
            id="priority-slider"
            v-model="defaultPriorityIndex"
            max="1"
            min="0"
            step="1"
            type="range"
          />
          <div class="d-flex justify-content-between">
            <small> {{ $t('t.views.admin.feedbacks.allocation.prioritization_weights.low') }} </small>
            <small> {{ $t('t.views.admin.feedbacks.allocation.prioritization_weights.heavy') }} </small>
          </div>

          <help-text
            id="prioritizationWeightExplanation"
            :params="{
              heavy: $t('t.views.admin.feedbacks.allocation.prioritization_weights.heavy'),
              low: $t('t.views.admin.feedbacks.allocation.prioritization_weights.low'),
              heavy_two: $t('t.views.admin.feedbacks.allocation.prioritization_weights.heavy')
            }"
            trans="t.views.admin.feedbacks.allocation.prioritization_weight_help"
          />
        </b-col>

        <b-col class="text-right" md="6">
          <b-button class="mr-2" variant="primary" @click.prevent="submitForm(false)">
            <i class="fas fa-magic mr-1"></i>
            {{ $t('t.views.admin.feedbacks.allocation.generate_allocation') }}
          </b-button>

          <b-button v-if="mappedAllocations.length" variant="outline-secondary" @click.prevent="submitForm(true)">
            <i class="fas fa-random mr-1"></i>
            {{ $t('t.views.admin.feedbacks.allocation.regenerate_allocation') }}
          </b-button>
        </b-col>
      </b-row>
      <b-alert v-if="errorMessage" show variant="danger">
        {{ errorMessage }}
      </b-alert>
    </form-basic>
    <b-card v-if="mappedAllocations.length"
            :header="$t('t.views.admin.feedbacks.allocation.allocations')">
      <form-basic :action="updateAssignmentAction">
        <b-row
          v-for="(assignment, index) in mappedAllocations"
          :key="index"
          class="align-items-center py-2 border-bottom"
        >
          <b-col cols="12" md="6">
            {{ assignment.participantName }}
          </b-col>
          <b-col cols="12" md="6">
            <multi-select
              :id="`allocation-${index}`"
              :key="assignment.participantId"
              v-model="assignment.trainerId"
              :multiple="true"
              :name="`feedbacks[${assignment.participantId}][users]`"
              :options="trainers"
              :show-clear="true"
              display-field="name"
            />
          </b-col>
        </b-row>

        <b-button
          class="float-right mt-3"
          type="submit"
          variant="primary"
        >
          {{ $t('t.views.admin.feedbacks.allocation.confirm_allocation') }}
        </b-button>
      </form-basic>
    </b-card>

  </div>
</template>

<script>
import MultiSelect from "../../MultiSelect";
import InputText from "../../form/InputText";
import InputMultiSelect from "../../form/InputMultiSelect.vue";
import FormBasic from "../../FormBasic.vue";

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
    const participantCount = this.participants.length;
    const trainerCount = this.trainers.length;
    const fairCapacity = trainerCount ? Math.ceil((participantCount || 1) / trainerCount) : 0;

    return {
      errorMessage: null,
      trainerPreferences: this.trainers.map(trainer => ({
        id: trainer.id,
        name: trainer.name,
        maxCapacity: fairCapacity,
        nogos: ""
      })),
      participantPreferences: this.participants.map(participant => ({
        id: participant.id,
        name: participant.scout_name,
        preferences: "",
        forbidden: ""
      })),
      mappedAllocations: [],
      defaultPriorityIndex: 1, // 0 = gering, 1 = stark
      priorityValues: [4, 100],
      participantCount: participantCount,

    };
  },
  computed: {
    trainersWithPrio() {
      return Object.fromEntries(this.participants.map(participant => {
        return [participant.id, this.trainers.map(trainer => ({
          ...trainer,
          name: this.prioPrefix(participant, trainer) + trainer.name
        }))]
      }))
    },
    numberOfWishes() {
      return Math.max(...this.participantPreferences.map((participant) => participant.preferences === "" ? 0 : participant.preferences.split(',').length))
    }
  },
  watch: {
    numberOfWishes(newVal) {
      this.priorityValues = [
        newVal + 1,
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
      participantToTrainer.sort((a, b) => a.participantName.localeCompare(b.participantName))
      return participantToTrainer;
    },
    prioPrefix(participant, trainer) {
      const preferences = this.participantPreferences.find(p => p.id===participant.id).preferences
      const prio = preferences.split(',').findIndex(id => id === '' + trainer.id)
      return prio === -1 ? '' : `${prio+1}. `
    },
    shuffleArray(array) {
      return array
        .map(value => ({value, sortKey: Math.random()}))
        .sort((a, b) => a.sortKey - b.sortKey)
        .map(({value}) => value);
    },
    submitForm(shuffle = false) {
      let trainerCapacities = this.trainerPreferences.map(trainer => [trainer.id, parseInt(trainer.maxCapacity)]);
      const numberOfWishes = this.numberOfWishes;
      let participantWishes = this.participantPreferences.map(participant => [
        participant.id,
        ...participant.preferences.split(',').map(pref => pref ? parseInt(pref) : null)
          .concat(Array(numberOfWishes).fill(null))
          .slice(0, numberOfWishes)
      ]);

      const forbiddenWishes = [];
      this.trainerPreferences.forEach(trainer => {
        if (trainer.nogos && trainer.nogos.length) trainer.nogos.split(',').forEach(pid => forbiddenWishes.push([parseInt(pid), trainer.id]));
      });
      this.participantPreferences.forEach(participant => {
        if (participant.forbidden && participant.forbidden.length) participant.forbidden.split(',').forEach(tid => forbiddenWishes.push([participant.id, parseInt(tid)]));
      });

      if (shuffle) {
        trainerCapacities = this.shuffleArray(trainerCapacities);
        participantWishes = this.shuffleArray(participantWishes);
      }

      const payload = {
        trainerCapacities,
        participantPreferences: participantWishes,
        numberOfWishes,
        forbiddenWishes,
        defaultPriority: this.priorityValues[this.defaultPriorityIndex],
      };

      window.axios.post(this.routeUri(...this.action), payload)
        .then(response => {
          this.errorMessage = null
          this.mappedAllocations = this.mapAllocationResults(response.data);
        })
        .catch(error => {
          console.error('Error', error.response);
          this.errorMessage = error.response.data.message
        });
    }

  }
};
</script>

<style scoped>

</style>
