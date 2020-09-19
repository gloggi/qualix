<template>
  <div>
    <div class="mb-3">
      <h5>{{ $t('t.views.quali_content.requirements_status')}}</h5>
      <requirement-progress :quali-requirements="quali.requirements"></requirement-progress>
    </div>

    <div class="d-flex justify-content-between mb-2">
      <button type="submit" class="btn btn-primary">{{ $t('t.global.save')}}</button>
      <div><a class="btn-link" href="#">Alle Anforderungen einklappen</a></div>
    </div>

    <div v-if="errors" class="invalid-feedback d-block" role="alert">
      <strong>{{ errors }}</strong>
    </div>

    <list-builder
      name="contents"
      :value="quali.contents">

      <template v-slot:text="{ value, remove }"><element-text v-model="value" :remove="remove" /></template>

      <template v-slot:observation="{ value, remove }"><element-observation v-model="value" :remove="remove" /></template>

      <template v-slot:requirement="{ value }"><element-requirement v-model="value">

        <list-builder :value="value.contents">

          <template v-slot:text="{ value, remove }"><element-text v-model="value" :remove="remove" /></template>

          <template v-slot:observation="{ value, remove }"><element-observation v-model="value" :remove="remove" /></template>

          <template v-slot:add-text="{ addElement }">
            <button-add @click="addElement" :payload="{ type: 'text', content: '', id: null }">{{ $t('t.views.quali_content.text_element') }}</button-add>
          </template>

        </list-builder>

      </element-requirement></template>

      <template v-slot:add-text="{ addElement }">
        <button-add @click="addElement" :payload="{ type: 'text', content: '', id: null }">{{ $t('t.views.quali_content.text_element') }}</button-add>
      </template>

    </list-builder>

  </div>
</template>

<script>
  import RequirementProgress from "./RequirementProgress"
  export default {
    name: 'FormQualiContent',
    components: {RequirementProgress},
    props: {
      quali: { type: Object, required: true }
    },
    data() {
      return {
      }
    },
    computed: {
      errors() {
        const errors = Object.values(window.errors)
        return errors && errors.length ? errors[0][0] : undefined;
      }
    },
    methods: {
    }
  }
</script>

<style scoped>

</style>
