<template>
  <div class="w-100">
    <div v-for="(round, roundIndex) in currentValue" class="form-group round-grid w-100 mb-4">
      <div v-for="(proposedGroup, groupIndex) in round" class="group-grid">
        <input-text
          class="group-grid-input mt-3 mb-2"
          :name="`${name}[${roundIndex}][${groupIndex}][group_name]`"
          :label="$t('t.views.admin.participant_group_generator.group_name')"
          v-model="proposedGroup.group_name"
          required="required"
          narrow-form />
        <input-hidden :name="`${name}[${roundIndex}][${groupIndex}][participants]`" :value="proposedGroup.participants"></input-hidden>
        <template v-for="participantId in proposedGroup.participants.split(',')">
          <participant-avatar
            v-if="participantFor(participantId)"
            :participant="participantFor(participantId)"
            :show-group="showGroup"></participant-avatar>
        </template>
      </div>
    </div>
  </div>
</template>

<script>
import Input from '../../mixins/input.js'
import ParticipantAvatar from './ParticipantAvatar.vue'
import InputHidden from '../form/InputHidden.vue'
import InputText from '../form/InputText.vue'

export default {
  name: 'InputGeneratedParticipantGroups',
  components: {InputText, InputHidden, ParticipantAvatar},
  mixins: [ Input ],
  props: {
    value: { type: Array, default: () => [] },
    participants: { type: Array, required: true },
    showGroup: { type: Boolean, default: false },
  },
  mounted() {
    this.$emit('input', this.currentValue)
  },
  methods: {
    participantFor(id) {
      return this.participants.find(participant => participant.id === parseInt(id))
    },
  },
}
</script>

<style scoped>

</style>
