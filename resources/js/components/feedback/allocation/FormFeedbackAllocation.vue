<template>
  <div>
    <form-basic :action="action">
      <b-card class="mb-4">
        <b-row>
          <b-col cols="12" md="6">
            <h2 class="h5 mb-2">{{ $t('t.views.admin.feedbacks.allocation.trainer_settings') }}</h2>
          </b-col>
        </b-row>
        <div>
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
                    :max="participantCount"
                    :min="0"
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
        </b-row>

        <b-table-simple responsive small striped>
          <b-thead>
            <b-tr>
              <b-th>{{ $t('t.views.admin.feedbacks.allocation.participant') }}</b-th>
              <b-th>
                {{ $t('t.views.admin.feedbacks.allocation.wishes') }}
              </b-th>
              <b-th>{{ $t('t.views.admin.feedbacks.allocation.nogo_header') }}</b-th>
            </b-tr>
          </b-thead>
          <b-tbody>
            <b-tr v-for="participant in participantPreferences" :key="`participant-${participant.id}`">
              <b-td>{{ participant.name }}</b-td>

              <b-td>
                <multi-select
                  :id="`participantPreference-${participant.id}`"
                  v-model="participant.preferences"
                  multiple
                  :options="trainersWithPrio[participant.id]"
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
        <b-col md="6">
          <label class="form-label mb-1" for="priority-slider">
            {{ $t('t.views.admin.feedbacks.allocation.prioritization_weight') }}</label>
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
          <b-button
            class="mr-2"
            variant="primary"
            @click.prevent="submitForm(false)"
          >
            <i class="fas fa-magic mr-1"></i>
            {{ $t('t.views.admin.feedbacks.allocation.generate_allocation') }}
          </b-button>

          <b-button
            v-if="mappedAllocations.length"
            variant="outline-secondary"
            @click.prevent="submitForm(true)"
          >
            <i class="fas fa-random mr-1"></i>
            {{ $t('t.views.admin.feedbacks.allocation.regenerate_allocation') }}
          </b-button>
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
                  :multiple="true"
                  :name="`feedbacks[${assignment.participantId}][users]`"
                  :options="trainers"
                  :show-clear="true"
                  display-field="name"
                />
              </b-td>
            </b-tr>
          </b-tbody>
        </b-table-simple>
        <b-button
          class="float-right"
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
</style>
