<?php

namespace App\Services\Validation;

class ValidQualiContentWithoutObservations extends ValidQualiContent {

    protected function getObservations() {
        return collect([]);
    }

}
