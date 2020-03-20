@php
    $listState = $qualiRequirements->map(function(\App\Models\QualiRequirement $requirement) {
        return [
            'id' => $requirement->id,
            'content' => $requirement->requirement->content,
            'notes' => $requirement->notes,
            'observations' => $requirement->observations->map(function(\App\Models\QualiObservation $observation) {
                return [
                    'id' => $observation->id,
                    'content' => $observation->observation->content,
                    'notes' => $observation->notes,
                    'block' => $observation->observation->block->name,
                    'date' => $observation->observation->block->block_date->formatLocalized('%A %d.%m.%Y'),
                ];
            }),
        ];
    });
@endphp

<drag-and-drop-list name="requirement_list" group="requirements" handle=".handle" :value="{{ json_encode($listState) }}">

    <template v-slot="{ list: requirements }">

        <b-card v-for="requirement in requirements" :key="requirement.id">
            <template #header><i class="fas fa-align-justify mr-2 handle"></i>@{{requirement.content}}</template>

            <p v-if="requirement.notes" class="multiline">@{{ requirement.notes }}</p>

            <drag-and-drop-list group="observations" handle=".handle" v-model="requirement.observations">
                <template v-slot="{ list: observations }">
                    <div v-for="observation in observations" :key="observation.id">

                        <b-card>
                            <i class="fas fa-align-justify mr-2 handle"></i>

                            <p class="multiline">@{{ observation.content }}</p>

                            <p class="card-text"><small class="text-muted">@{{ observation.block }}, @{{ observation.date }}</small></p>
                        </b-card>

                        <p class="multiline">@{{ observation.notes }}</p>

                    </div>
                </template>
            </drag-and-drop-list>

        </b-card>

    </template>

</drag-and-drop-list>
