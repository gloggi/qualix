<template>
  <b-table-simple
    hover
    class="table-responsive-cards"
    v-bind="$attrs">
    <b-thead>
      <b-tr>
        <th v-for="col in fields">{{ col.label }}</th>
        <th v-if="showActions" class="actions"></th>
      </b-tr>
    </b-thead>
    <b-tbody>
      <b-tr v-for="(row, rowIdx) in data" :key="row.id">
        <th v-if="isHeaderRow(row)" :colspan="numCols">{{ row.text }}</th>
        <template v-else>
          <template v-for="(col, idx) in fields">
            <td v-if="isSlotCol(col)" :class="calculateCellClass(call(col.value, row), row, col, rowIdx, idx)" :data-label="col.label"><slot :name="col.slot" :row="row"></slot></td>
            <td v-else-if="isImageCol(col) && isLinkCol(col)" :class="calculateCellClass(call(col.value, row), row, col, rowIdx, idx)" :data-label="col.label"><a :href="call(col.href, row)"><img v-if="call(col.value, row)" :src="call(col.value, row)" class="avatar-small"/></a></td>
            <td v-else-if="isImageCol(col)" :class="calculateCellClass(call(col.value, row), row, col, rowIdx, idx)" :data-label="col.label"><img v-if="call(col.value, row)" :src="call(col.value, row)" class="avatar-small"/></td>
            <td v-else-if="isLinkCol(col)" :class="calculateCellClass(call(col.value, row), row, col, rowIdx, idx)" :data-label="col.label"><a :href="call(col.href, row)">{{ call(col.value, row) }}</a></td>
            <td v-else :class="calculateCellClass(call(col.value, row), row, col, rowIdx, idx)" :data-label="col.label">{{ call(col.value, row) }}</td>
          </template>
          <td v-if="showActions" class="actions">
            <template v-for="(action, name) in actions">
              <template v-if="name === 'delete'">
                <a v-if="name === 'delete'" class="text-danger" @click="$bvModal.show(modalId(row))" :title="$t('t.global.delete')">
                  <i class="fas fa-minus-circle"></i>
                </a>
                <modal-delete :id="modalId(row)" v-bind="call(action, row)"></modal-delete>
              </template>
              <template v-else>
                <a :href="call(action, row)">
                  <i :class="`fas fa-${name}`" :title="actionTitle(name)"></i>
                </a>
              </template>
            </template>
          </td>
        </template>
      </b-tr>
    </b-tbody>
  </b-table-simple>
</template>

<script>
import { isEmpty } from 'lodash'

export default {
  name: 'ResponsiveTable',
  props: {
    data: { type: Array, default: () => [] },
    actions: { type: Object, default: () => {} },
    fields: { type: Array, default: () => [] },
    image: { type: Array, default: () => [] },
    cellClass: { type: [String, Function], default: '' },
    imageCellClass: { type: [String, Function], default: '' },
  },
  computed: {
    showActions() {
      return !isEmpty(this.actions)
    },
    numCols() {
      return this.fields.length + (this.showActions ? 1 : 0)
    },
  },
  methods: {
    modalId(row) {
      return this.$options.filters.kebabCase('delete-' + row.id)
    },
    isHeaderRow(row) {
      return row.type === 'header'
    },
    isSlotCol(col) {
      return col.slot !== undefined
    },
    isImageCol(col) {
      return col.type === 'image'
    },
    isLinkCol(col) {
      return col.href !== undefined
    },
    call(func, row) {
      if (typeof func !== 'function') return func
      return func.call(this, row)
    },
    actionTitle(name) {
      return this.$te(`t.global.${name}`) ? this.$t(`t.global.${name}`) : ''
    },
    calculateCellClass(cellValue, row, colLabel, rowIdx, colIdx) {
      return this.call(this.cellClass, { cellValue, row, colLabel, rowIdx, colIdx })
    },
  }
}
</script>

<style scoped>

</style>
