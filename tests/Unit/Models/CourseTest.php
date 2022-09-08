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
}
