@component('components.form', ['route' => ['qualiContent.update', ['course' => $course->id, 'participant' => $participant->id, 'quali' => $quali->id]]])
    <div class="d-flex justify-content-between mb-2">
        <button type="submit" class="btn btn-primary">{{__('t.global.save')}}</button>
        <div><a class="btn-link" href="#">Alle Anforderungen einklappen</a></div>
    </div>

    <list-builder
        name="contents"
        :value="{{ json_encode($quali->contents) }}"
        :translations="{{ json_encode($translations) }}">
        <template v-slot:title="{ value }"><element-title v-model="value" /></template>
        <template v-slot:text="{ value }"><element-text v-model="value" /></template>
        <template v-slot:observation="{ value, translations }"><element-observation v-model="value" :translations="translations" /></template>
        <template v-slot:requirement="{ value, translations }">
            <element-requirement v-model="value" :translations="translations">
                <list-builder :value="value.contents" :translations="translations">
                    <template v-slot:title="{ value }"><element-title v-model="value" /></template>
                    <template v-slot:text="{ value }"><element-text v-model="value" /></template>
                    <template v-slot:observation="{ value, translations }"><element-observation v-model="value" :translations="translations" /></template>
                </list-builder>
            </element-requirement>
        </template>
    </list-builder>
@endcomponent
