<?php

namespace App\Exceptions;

class RequirementsOutdatedException extends \Exception {

    protected $correctedContents;

    public function __construct($correctedContents) {
        parent::__construct();
        $this->correctedContents = $correctedContents;
    }

    public function getCorrectedContents() {
        return $this->correctedContents;
    }
}
