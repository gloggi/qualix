<?php

namespace App\Services\Import\Participants;

use App\Models\Course;
use Illuminate\Support\Collection;

interface ParticipantListParser{

    /**
     * Parses participants from an uploaded file and returns the data in an array.
     *
     * @param string $filePath path to input file containing participants
     * @return Collection list of participants that were parsed from the input file
     */
    public function parse(string $filePath);
}
