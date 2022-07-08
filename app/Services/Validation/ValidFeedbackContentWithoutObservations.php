<?php

namespace App\Services\Validation;

class ValidFeedbackContentWithoutObservations extends ValidFeedbackContent {

    protected function getObservations() {
        return collect([]);
    }

}
