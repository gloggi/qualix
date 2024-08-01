<template>
  <form-basic :action="action">

    <input-multi-select
      name="participants"
      v-model="participantsValue"
      :label="$t('t.models.observation.participants')"
      required
      :options="allParticipants"
      :groups="allParticipantGroups"
      display-field="scout_name"
      :autofocus="autofocusParticipants"
      multiple></input-multi-select>

    <input-textarea
      name="content"
      v-model="contentValue"
      :label="$t('t.models.observation.content')"
      required
      :autofocus="!autofocusParticipants"
      :limit="contentCharLimit"
      v-slot="slotProps">
      <char-limit :current-value="slotProps.currentValue" :limit="slotProps.limit"></char-limit>
    </input-textarea>

    <input-multi-select
      name="block"
      v-model="blockValue"
      :label="$t('t.models.observation.block')"
      required
      :options="allBlocks"
      display-field="blockname_and_number"
      @input="onBlockUpdate">
      <template #below="{ value }">
        <button-new-evaluation-grid
          :course-id="courseId"
          :block-id="value"
          :participant-ids="participantsValue"
          :evaluation-grid-templates-mapping="evaluationGridTemplatesMapping">
          {{$t('t.views.observations.evaluation_grid_templates_available')}}
        </button-new-evaluation-grid>
      </template>
    </input-multi-select>

    <input-multi-select
      v-if="allRequirements.length"
      name="requirements"
      v-model="requirementsValue"
      :label="$t('t.models.observation.requirements')"
      :options="allRequirements"
      display-field="content"
      multiple></input-multi-select>

    <input-radio-button
      v-if="useImpressions"
      name="impression"
      v-model="impressionValue"
      :label="$t('t.models.observation.impression')"
      required
      :options="{ '2': $t('t.global.positive'), '1': $t('t.global.neutral'), '0': $t('t.global.negative') }"></input-radio-button>

    <input-multi-select
      v-if="allCategories.length"
      name="categories"
      v-model="categoriesValue"
      :label="$t('t.models.observation.categories')"
      :options="allCategories"
      display-field="name"
      multiple></input-multi-select>

    <button-submit></button-submit>

  </form-basic>
</template>

<script>

export default {
  name: 'FormObservation',
  props: {
    action: { type: Array, required: true },
    participants: { type: String, default: '' },
    allParticipants: { type: Array, required: true },
    allParticipantGroups: { type: Object, required: true },
    autofocusParticipants: { type: Boolean, default: false },
    content: { type: String, default: '' },
    contentCharLimit: { type: Number, required: true },
    blockRequirementsMapping: { type: Array, default: () => ([]) },
    block: { type: String, default: '' },
    allBlocks: { type: Array, required: true },
    courseId: { type: Number, required: true },
    evaluationGridTemplatesMapping: { type: Object, default: () => ({}) },
    requirements: { type: String, default: '' },
    allRequirements: { type: Array, default: () => ([]) },
    useImpressions: { type: Boolean, default: false },
    impression: { type: String, default: '1' },
    categories: { type: String, default: '' },
    allCategories: { type: Array, default: () => ([]) },
  },
  data: function () {
    return {
      participantsValue: this.participants,
      contentValue: this.content,
      blockValue: this.block,
      requirementsValue: this.requirements,
      impressionValue: this.impression,
      categoriesValue: this.categories,
      ignoreNextBlockUpdateEvent: !!this.requirementsValue,
    }
  },
  methods: {
    onBlockUpdate (blockId) {
      if (!this.blockRequirementsMapping.length) return

      // If we have old input, ignore the first input event from the block selection component,
      // so we don't overwrite the old value of the requirements selection component initially.
      if (this.ignoreNextBlockUpdateEvent) {
        this.ignoreNextBlockUpdateEvent = false
        return
      }

      const block = this.blockRequirementsMapping.find(b => ('' + b.id) === blockId)
      if (blockId != null && block !== undefined) {
        this.requirementsValue = block.requirement_ids.join()
      }
    }
  }
}
</script>

<style scoped>

</style>
