<?php

namespace App\Services\Import\Participants;

use App\Models\Course;
use App\Models\Participant;
use App\Services\Import\Participants\ParticipantListParser;
use Illuminate\Support\Collection;

abstract class ParticipantListImporter {

    /** @var ParticipantListParser */
    protected $parser;

    public function __construct(ParticipantListParser $parser) {
        $this->parser = $parser;
    }

    /**
     * Reads participants from an uploaded file and saves them to the database.
     * In case a participants already exists in the database, nevertheless they will be added.
     *
     * @param string $filePath path to input file containing participants
     * @param Course $course course into which the participants are imported
     * @return Collection list of participants that were imported to the database
     */
    public function import(string $filePath, Course $course) {
        $parsedParticipants = $this->parser->parse($filePath);

        return $parsedParticipants->map(function ($parsedParticipant) use($course) {
            return Participant::create([
                'scout_name' => $parsedParticipant['scout_name'],
                'course_id' => $course->id,
                'group' => $parsedParticipant['group']]);
        });
    }
}
