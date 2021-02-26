<?php

namespace Tests\Unit\Models;

use App\Models\Course;
use App\Models\Observation;
use App\Models\Quali;
use Tests\TestCaseWithBasicData;

class CourseTest extends TestCaseWithBasicData {

    public function test_qualisUsingObservations_worksAsExpected() {
        // given
        /** @var Observation $observation */
        $observation = Observation::find($this->createObservation());
        $participantObservationId = $observation->participants()->withPivot('id')->first()->pivot->id;
        $quali = Quali::find($this->createQuali());
        $quali->participant_observations()->sync([$participantObservationId => ['order' => 1]]);
        $course = Course::find($this->courseId);

        // when
        $qualisUsingObservations = $course->qualis_using_observations;

        // then
        $this->assertEquals([$observation->id => [$quali->display_name]], collect($qualisUsingObservations)->toArray());
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
}
