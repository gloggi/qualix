<template>
  <div>

    <b-button variant="link" block class="mb-2 text-left" v-b-toggle.filters-collapse v-if="anyRequirements || anyCategories">
      <i class="fas fa-filter"></i> {{ $t('t.views.participant_details.filter') }} <i class="fas fa-caret-down"></i>
    </b-button>

    <b-collapse id="filters-collapse" :visible="filtersVisibleInitially" v-if="anyRequirements || anyCategories || null !== usedObservations">
      <b-row>

        <b-col cols="12" md="6" v-if="anyRequirements">
          <multi-select
            id="filter-requirements"
            name="filter-requirements"
            class="form-control-multiselect"
            :selected.sync="selectedRequirement"
            :allow-empty="true"
            :placeholder="$t('t.views.participant_details.filter_by_requirement')"
            :options="requirementOptions"
            :multiple="false"
            :close-on-select="true"
            :show-labels="false"
            :show-clear="true"
            display-field="content"></multi-select>
        </b-col>

        <b-col cols="12" md="6" v-if="anyCategories">
          <multi-select
            id="filter-categories"
            name="filter-categories"
            class="form-control-multiselect"
            :selected.sync="selectedCategory"
            :allow-empty="true"
            :placeholder="$t('t.views.participant_details.filter_by_category')"
            :options="categoryOptions"
            :multiple="false"
            :close-on-select="true"
            :show-labels="false"
            :show-clear="true"
            display-field="name"></multi-select>
        </b-col>

        <b-col cols="12" md="6" v-if="null !== usedObservations">
          <label for="hide-already-used-observations" class="d-flex w-100 h-100 align-items-center">
            <b-form-checkbox
              type="checkbox"
              id="hide-already-used-observations"
              v-model="hideUsedObservations"
              :switch="true"
              size="xl"
            ></b-form-checkbox>
            <span>{{ $t('t.views.participant_details.hide_already_used_observations') }}</span>
          </label>
        </b-col>

      </b-row>
    </b-collapse>

    <responsive-table
      class="mt-3 mt-lg-0"
      :data="filteredObservations"
      :actions="actions"
      :fields="fields"
      :cell-class="pointerCursor ? 'cursor-pointer' : ''"
      @clickCell="onClickObservation">

      <template v-slot:observation-content="{ row: observation }">
        <observation-content :observation="observation"></observation-content>
      </template>

      <template v-slot:requirements="{ row: observation }">
        <template v-for="requirement in observation.requirements">
          <span class="white-space-normal badge" :class="requirement.mandatory ? 'badge-warning' : 'badge-info'">{{ requirement.content }}</span>
        </template>
      </template>

      <template v-slot:categories="{ row: observation }">
        <template v-for="category in observation.categories">
          <span class="white-space-normal badge badge-primary">{{ category.name }}</span>
        </template>
      </template>

      <template v-slot:impression="{ row: observation }">
        <span v-if="observation.impression === 0" class="badge badge-danger">{{ $t('t.global.negative') }}</span>
        <span v-else-if="observation.impression === 2" class="badge badge-success">{{ $t('t.global.positive') }}</span>
        <span v-else class="badge badge-secondary">{{ $t('t.global.neutral') }}</span>
      </template>

    </responsive-table>

    <div v-if="!filteredObservations.length" class="text-center min-vh-50">{{ $t('t.global.no_options') }}</div>

  </div>
</template>

<script>

import { isEmpty } from 'lodash'
import ResponsiveTable from "./ResponsiveTable"
import ObservationContent from "./ObservationContent"
export default {
  name: 'ObservationList',
  components: {ResponsiveTable, ObservationContent},
  props: {
    courseId: { type: String },
    observations: { type: Array, default: () => [] },
    actions: { type: Object, default: () => {} },
    requirements: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    usedObservations: { type: Array, default: null },
    showContent: { type: Boolean, default: false },
    showBlock: { type: Boolean, default: false },
    showRequirements: { type: Boolean, default: false },
    showCategories: { type: Boolean, default: false },
    showImpression: { type: Boolean, default: false },
    showUser: { type: Boolean, default: false },
    pointerCursor: { type: Boolean, default: false },
  },
  data() {
    return {
      selectedRequirement: null,
      selectedCategory: null,
      hideUsedObservations: false,
    }
  },
  computed: {
    fields() {
      const fields = []
      if (this.showContent) fields.push({ label: this.$t('t.models.observation.content'), slot: 'observation-content' })
      if (this.showBlock) fields.push({ label: this.$t('t.models.observation.block'), value: observation => observation.block.blockname_and_number })
      if (this.showRequirements) fields.push({ label: this.$t('t.models.observation.requirements'), slot: 'requirements' })
      if (this.showCategories) fields.push({ label: this.$t('t.models.observation.categories'), slot: 'categories' })
      if (this.showImpression) fields.push({ label: this.$t('t.models.observation.impression'), slot: 'impression' })
      if (this.showUser) fields.push({ label: this.$t('t.models.observation.user'), value: observation => observation.user.name })
      return fields
    },
    filtersVisibleInitially() {
      return !!(this.selectedRequirement || this.selectedCategory)
    },
    requirementOptions() {
      return [...this.requirements, this.noRequirementOption]
    },
    noRequirementOption() {
      return {content: '-- ' + this.$t('t.views.participant_details.observations_without_requirement') + ' --', id: 0}
    },
    categoryOptions() {
      return [...this.categories, this.noCategoryOption]
    },
    noCategoryOption() {
      return {name: '-- ' + this.$t('t.views.participant_details.observations_without_category') + ' --', id: 0}
    },
    filteredObservations() {
      return this.observations
        .filter(observation =>
          this.selectedRequirement === null ||
          (this.selectedRequirement.id === 0 && isEmpty(observation.requirements)) ||
          observation.requirements.map(requirement => requirement.id).includes(this.selectedRequirement.id))
        .filter(observation =>
          this.selectedCategory === null ||
          (this.selectedCategory.id === 0 && isEmpty(observation.categories)) ||
          observation.categories.map(category => category.id).includes(this.selectedCategory.id))
        .filter(observation =>
          this.usedObservations === null ||
          !this.hideUsedObservations ||
          !this.usedObservations.includes(observation.pivot.id))
    },
    anyRequirements() {
      return this.requirements.length > 0
    },
    anyCategories() {
      return this.categories.length > 0
    },
    allStorage() {
      return JSON.parse(localStorage.courses ?? '{}') || {}
    },
    storage() {
      if (!this.courseId) return {}
      return this.allStorage[this.courseId] || {}
    }
  },
  methods: {
    persistFilter(key, value) {
      if (!this.courseId) return
      this.storage[key] = value ? value : null;
      const alteredStorage = this.allStorage
      alteredStorage[this.courseId] = this.storage
      localStorage.courses = JSON.stringify(alteredStorage)
    },
    onClickObservation(...args) {
      this.$emit('clickObservation', ...args)
    },
  },
  mounted() {
    if (!this.courseId || !localStorage.courses) return

    if (this.anyRequirements) {
      const storedRequirement = this.storage.selectedRequirement
      if (storedRequirement !== null) {
        if (storedRequirement === 0) this.selectedRequirement = this.noRequirementOption
        else this.selectedRequirement = this.requirements.find(req => req.id === storedRequirement) ?? null;
      }
    }

    if (this.anyCategories) {
      const storedCategory = storage.selectedCategory
      if (storedCategory !== null) {
        if (storedCategory === 0) this.selectedCategory = this.noCategoryOption
        else this.selectedCategory = this.categories.find(cat => cat.id === storage.selectedCategory) ?? null;
      }
    }
  },
  watch: {
    selectedRequirement() {
      this.persistFilter('selectedRequirement', this.selectedRequirement?.id)
    },
    selectedCategory() {
      this.persistFilter('selectedCategory', this.selectedCategory?.id)
    },
    hideUsedObservations() {
      this.persistFilter('hideUsedObservations', this.hideUsedObservations)
    },
  },
}
</script>

<style scoped>

</style>
