<?php

namespace Tests;

use App\Models\Observation;
use App\Models\ObservationAssignment;
use App\Models\ParticipantGroup;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

abstract class TestCaseWithBasicData extends TestCaseWithCourse
{
    protected $participantId;
    protected $blockId;

    public function setUp(): void {
        parent::setUp();

        $this->participantId = $this->createParticipant('Pflock');
        $this->blockId = $this->createBlock('Block 1', '1.1', '01.01.2019', null);
    }

    protected function createObservation($content = 'hat gut mitgemacht', $impression = 1, $requirementId = [], $categoryIds = [], $blockId = null, $participantIds = null, $userId = null) {
        $observation = Observation::create(['user_id' => ($userId !== null ? $userId : $this->user()->id), 'block' => ($blockId !== null ? $blockId : $this->blockId), 'content' => $content, 'impression' => $impression]);
        $observation->requirements()->attach($requirementId);
        $observation->categories()->attach($categoryIds);
        $observation->participants()->attach(($participantIds !== null ? Arr::wrap($participantIds) : [$this->participantId]));
        return $observation->id;
    }

    protected function createParticipantGroup($group_name = "Test Gruppe", $participantIds = null, $courseId = null) {
        $participant_group = ParticipantGroup::create(['course_id' => ($courseId !== null ? $courseId : $this->courseId), 'group_name' => $group_name]);
        $participant_group->participants()->attach(($participantIds !== null ? Arr::wrap($participantIds) : [$this->participantId]));
        return $participant_group->id;
    }

    protected function createObservationAssignment($name = "Test Auftrag", $participantIds = null, $blockIds = null, $userIds = null, $courseId = null) {
        $observation_assignment = ObservationAssignment::create(['course_id' => ($courseId !== null ? $courseId : $this->courseId), 'name' => $name]);
        $observation_assignment->participants()->attach(($participantIds !== null ? Arr::wrap($participantIds) : [$this->participantId]));
        $observation_assignment->blocks()->attach(($blockIds !== null ? Arr::wrap($blockIds) : [$this->blockId]));
        $observation_assignment->users()->attach(($userIds !== null ? Arr::wrap($userIds) : [Auth::id()]));
        return $observation_assignment->id;
    }
}
