<?php

namespace App\Observers;

use App\Models\Observation;
use App\Models\Participant;

class DeleteOrphanObservationsOnParticipantDelete {
    /**
     * Handle the deletion of a participant.
     *
     * @param Participant $participant that was deleted
     * @return void
     */
    public function deleted(Participant $participant) {
        Observation::whereDoesntHave('participants')->delete();
    }
}
