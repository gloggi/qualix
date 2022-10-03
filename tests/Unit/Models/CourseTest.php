<?php

namespace Tests\Unit\Models;

use App\Models\Course;
use App\Models\Feedback;
use App\Models\Observation;
use Tests\TestCaseWithBasicData;

class CourseTest extends TestCaseWithBasicData {

    public function test_feedbacksUsingObservations_worksAsExpected() {
        // given
        /** @var Observation $observation */
        $observation = Observation::find($this->createObservation());
        $participantObservationId = $observation->participants()->withPivot('id')->first()->pivot->id;
        $feedback = Feedback::find($this->createFeedback());
        $feedback->participant_observations()->sync([$participantObservationId => ['order' => 1]]);
        $course = Course::find($this->courseId);
        $observations = [$observation];

        // when
        $feedbacksUsingObservations = $course->feedbacksUsingObservations($observations);

        // then
        $this->assertEquals([$observation->id => [$feedback->display_name]], collect($feedbacksUsingObservations)->toArray());
    }

    public function test_usesRequirements_isTrueWhenCourseHasRequirements() {
        // given
        $this->createRequirement();
        $course = Course::find($this->courseId);

        // when
        $usesRequirements = $course->uses_requirements;

        // then
        $this->assertEquals(true, $usesRequirements);
    }

    public function test_usesRequirements_isFalseWhenCourseHasNoRequirements() {
        // given
        $course = Course::find($this->courseId);

        // when
        $usesRequirements = $course->uses_requirements;

        // then
        $this->assertEquals(false, $usesRequirements);
    }

    public function test_usesCategories_isTrueWhenCourseHasCategories() {
        // given
        $this->createCategory();
        $course = Course::find($this->courseId);

        // when
        $usesCategories = $course->uses_categories;

        // then
        $this->assertEquals(true, $usesCategories);
    }

    public function test_usesCategories_isFalseWhenCourseHasNoCategories() {
        // given
        $course = Course::find($this->courseId);

        // when
        $usesCategories = $course->uses_categories;

        // then
        $this->assertEquals(false, $usesCategories);
    }

    public function test_defaultRequirementStatusId_isTheFirstRequirementStatusIdInTheCourse() {
        // given
        $course = Course::find($this->courseId);
        $course2 = $this->createCourse();
        $course->requirement_statuses()->delete();
        $this->createRequirementStatus('erfüllt', 'green', 'circle-check', $course2);
        $expected = $this->createRequirementStatus('erfüllt', 'green', 'circle-check', $this->courseId);
        $this->createRequirementStatus('nicht erfüllt', 'red', 'circle-xmark', $this->courseId);

        // when
        $defaultRequirementStatusId = $course->default_requirement_status_id;

        // then
        $this->assertEquals($expected, $defaultRequirementStatusId);
    }

    public function test_getNextParticipant_returnsSecondParticipantForFirst() {
        // given
        $course = Course::find($this->courseId);
        $first = $course->participants->first();
        $second = $course->participants->get(1);

        // when
        $next = $course->getNextParticipant($first);

        // then
        $this->assertEquals($second, $next);
    }

    public function test_getNextParticipant_returnsNoParticipantForLast() {
        // given
        $course = Course::find($this->courseId);
        $last = $course->participants->last();

        // when
        $next = $course->getNextParticipant($last);

        // then
        $this->assertEquals(false, $next);
    }

    public function test_getPreviousParticipant_returnsSecondToLastParticipantForLast() {
        // given
        $course = Course::find($this->courseId);
        $participants = $course->participants;
        $last = $participants->last();
        $secondToLast = $participants->get(count($participants) - 2);

        // when
        $previous = $course->getPreviousParticipant($last);

        // then
        $this->assertEquals($secondToLast, $previous);
    }

    public function test_getPreviousParticipant_returnsNoParticipantForFirst() {
        // given
        $course = Course::find($this->courseId);
        $first = $course->participants->first();

        // when
        $previous = $course->getPreviousParticipant($first);

        // then
        $this->assertEquals(false, $previous);
    }
}
