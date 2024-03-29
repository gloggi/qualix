<template>
  <div>
    <b-row>
      <b-col sm>
        <b-button variant="link" block class="mb-2 text-left" v-b-toggle.filters-collapse>
          <i class="fas fa-filter"></i> {{ $t('t.views.participant_details.filter') }} <i class="fas fa-caret-down"></i>
        </b-button>
      </b-col>
      <b-col v-if="anyFilterActive" class="text-right">
        <p class="mb-0">
          {{$tc('t.views.participant_details.shown_observations', 0, {filtered: filteredObservations.length, total: totalObservations})}} -
          <b-button variant="link" class="mb-2 px-0 align-baseline" :visible="anyFilterActive" @click="clearAllFilters">
            {{$t('t.views.participant_details.show_all')}}
          </b-button>
        </p>
      </b-col>
    </b-row>

    <b-collapse id="filters-collapse" :visible="filtersVisibleInitially">
      <b-row>

        <b-col class="mb-2" cols="12" md="6" v-if="anyRequirements">
          <multi-select
            id="filter-requirements"
            name="filter-requirements"
            :class="{'form-control-multiselect':true, 'background-color-on-selection':selectedRequirements.length>0}"
            :selected.sync="selectedRequirements"
            :allow-empty="true"
            :placeholder="$t('t.views.participant_details.filter_by_requirement')"
            :options="requirementOptions"
            :multiple="true"
            :close-on-select="true"
            :show-labels="false"
            :show-clear="false"
            display-field="content"></multi-select>
        </b-col>

        <b-col class="mb-2" cols="12" md="6" v-if="anyCategories">
          <multi-select
            id="filter-categories"
            name="filter-categories"
            :class="{'form-control-multiselect':true, 'background-color-on-selection':selectedCategories.length>0}"
            :selected.sync="selectedCategories"
            :allow-empty="true"
            :placeholder="$t('t.views.participant_details.filter_by_category')"
            :options="categoryOptions"
            :multiple="true"
            :close-on-select="true"
            :show-labels="false"
            :show-clear="false"
            display-field="name"></multi-select>
        </b-col>

        <b-col class="mb-2" cols="12" md="6">
          <multi-select
            id="filter-authors"
            name="filter-authors"
            :class="{'form-control-multiselect':true, 'background-color-on-selection':selectedAuthor!==null}"
            :selected.sync="selectedAuthor"
            :allow-empty="true"
            :placeholder="$t('t.views.participant_details.filter_by_author')"
            :options="authors"
            :multiple="false"
            :close-on-select="true"
            :show-labels="false"
            :show-clear="true"
            display-field="name"></multi-select>
        </b-col>

        <b-col class="mb-2" cols="12" md="6">
          <multi-select
            id="filter-blocks"
            name="filter-blocks"
            :class="{'form-control-multiselect':true, 'background-color-on-selection':selectedBlock!==null}"
            :selected.sync="selectedBlock"
            :allow-empty="true"
            :placeholder="$t('t.views.participant_details.filter_by_block')"
            :options="blocks"
            :multiple="false"
            :close-on-select="true"
            :show-labels="false"
            :show-clear="true"
            display-field="name"></multi-select>
        </b-col>

        <b-col class="mb-2" cols="12" md="6" v-if="null !== usedObservations">
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
      <b-row>
        <b-col sm>
          <help-text id="filterExplanationHelp" trans="t.views.participant_details.filter_explanation_help"></help-text>
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
import MultiSelect from './MultiSelect'
import HelpText from './HelpText'

export default {
  name: 'ObservationList',
  components: {MultiSelect, ResponsiveTable, ObservationContent, HelpText},
  props: {
    courseId: { type: String },
    observations: { type: Array, default: () => [] },
    actions: { type: Object, default: () => {} },
    requirements: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    authors: { type: Array, default: () => [] },
    blocks: { type: Array, default: () => [] },
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
      selectedRequirements: [],
      selectedCategories: [],
      selectedAuthor: null,
      selectedBlock: null,
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
      return !!(!isEmpty(this.selectedRequirements) || !isEmpty(this.selectedCategories) || this.selectedAuthor || this.selectedBlock)
    },
    anyFilterActive() {
      return this.filtersVisibleInitially || this.hideUsedObservations
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
          isEmpty(this.selectedRequirements) ||
          (this.selectedRequirements.some(selectedRequirement => selectedRequirement.id === 0) && isEmpty(observation.requirements)) ||
          observation.requirements.map(requirement => requirement.id).some(requirement => this.selectedRequirements.map(selectedRequirement => selectedRequirement.id).includes(requirement)))
        .filter(observation =>
          isEmpty(this.selectedCategories) ||
          (this.selectedCategories.some(selectedCategory => selectedCategory.id === 0) && isEmpty(observation.categories)) ||
          observation.categories.map(category => category.id).some(category => this.selectedCategories.map(selectedCategory => selectedCategory.id).includes(category)))
        .filter(observation =>
          this.selectedAuthor === null ||
          observation.user.id ===  this.selectedAuthor.id)
        .filter(observation =>
          this.selectedBlock === null ||
          observation.block.id === this.selectedBlock.id)
        .filter(observation =>
          this.usedObservations === null ||
          !this.hideUsedObservations ||
          !this.usedObservations.includes(observation.pivot.id))
    },
    totalObservations() {
      return this.observations.length;
    },
    anyRequirements() {
      return this.requirements.length > 0
    },
    anyCategories() {
      return this.categories.length > 0
    },
    allStorage() {
      return JSON.parse(localStorage.getItem('courses') ?? '{}') || {}
    },
    storage() {
      if (!this.courseId) return {}
      return this.allStorage[this.courseId] || {}
    }
  },
  methods: {
    clearAllFilters() {
      this.hideUsedObservations = false;
      this.selectedAuthor = null;
      this.selectedBlock = null;
      this.selectedCategories = [];
      this.selectedRequirements = [];
    },
    persistFilter(key, value) {
      if (!this.courseId) return
      this.storage[key] = value != null ? value : null;
      const alteredStorage = this.allStorage
      alteredStorage[this.courseId] = this.storage
      localStorage.setItem('courses', JSON.stringify(alteredStorage))
    },
    onClickObservation(...args) {
      this.$emit('clickObservation', ...args)
    },
  },
  mounted() {
    if (!this.courseId || !window.localStorage.getItem('courses')) return

    if (this.anyRequirements) {
      const selectedRequirements = this.storage.selectedRequirements
      if (!isEmpty(selectedRequirements)) {
        this.selectedRequirements = selectedRequirements.map(e => {
          if (e === 0) return this.noRequirementOption;
          else return this.requirements.find(req => req.id === e)
          }
        )
      }
    }

    if (this.anyCategories) {
      const selectedCategories = this.storage.selectedCategories
      if (!isEmpty(selectedCategories)) {
        this.selectedCategories = selectedCategories.map(e => {
            if (e === 0) return this.noCategoryOption;
            else return this.categories.find(cat => cat.id === e)
          }
        )
      }
    }

    const storedAuthor = this.storage.selectedAuthor
    if (storedAuthor !== null) {
      this.selectedAuthor = this.authors.find(author => author.id === storedAuthor) ?? null;
    }

    const storedBlock = this.storage.selectedBlock
    if (storedBlock !== null) {
      this.selectedBlock = this.blocks.find(block => block.id === storedBlock) ?? null;
    }

    if (this.usedObservations !== null) {
      this.hideUsedObservations = !!this.storage.hideUsedObservations
    }
  },
  watch: {
    selectedRequirements() {
      this.persistFilter('selectedRequirements', this.selectedRequirements?.map(selectedRequirement => selectedRequirement.id))
    },
    selectedCategories() {
      this.persistFilter('selectedCategories',  this.selectedCategories?.map(selectedCategory => selectedCategory.id))
    },
    selectedAuthor() {
      this.persistFilter('selectedAuthor', this.selectedAuthor?.id)
    },
    selectedBlock() {
      this.persistFilter('selectedBlock', this.selectedBlock?.id)
    },
    hideUsedObservations() {
      this.persistFilter('hideUsedObservations', this.hideUsedObservations)
    },
  },
}
</script>

<style>
</style>
