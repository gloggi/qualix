<?php

namespace Tests;

use App\Models\Block;
use App\Models\Category;
use App\Models\Course;
use App\Models\FeedbackData;
use App\Models\Participant;
use App\Models\Requirement;
use Egulias\EmailValidator\EmailLexer;
use Egulias\EmailValidator\Validation\DNSCheckValidation;

abstract class TestCaseWithCourse extends TestCase
{
    protected $courseId;

    public function setUp(): void {
        parent::setUp();

        $this->courseId = $this->createCourse();
    }

    protected function createParticipant($scoutName = 'Pflock', $courseId = null) {
        return Participant::create(['course_id' => ($courseId !== null ? $courseId : $this->courseId), 'scout_name' => $scoutName])->id;
    }

    protected function createBlock($blockname = 'Block 1', $fullBlockNumber = '1.1', $date = '01.01.2019', $requirementIds = null, $courseId = null) {
        $block = Block::create(['course_id' => ($courseId !== null ? $courseId : $this->courseId), 'full_block_number' => $fullBlockNumber, 'name' => $blockname, 'block_date' => $date]);
        $block->requirements()->attach($requirementIds);
        return $block->id;
    }

    protected function createCategory($name = 'Kategorie 1', $courseId = null) {
        return Category::create(['course_id' => ($courseId !== null ? $courseId : $this->courseId), 'name' => $name])->id;
    }

    protected function createRequirement($content = 'Mindestanforderung 1', $mandatory = true, $courseId = null) {
        return Requirement::create(['course_id' => ($courseId !== null ? $courseId : $this->courseId), 'content' => $content, 'mandatory' => $mandatory])->id;
    }

    protected function createFeedback($name = 'Zwischenquali', $courseId = null) {
        $course = Course::find($courseId === null ? $this->courseId : $courseId);
        if (!$course->participants()->exists()) $this->createParticipant('Feedback-TN', $course->id);
        $feedbackData = FeedbackData::create(['name' => $name, 'course_id' => $course->id]);
        return $feedbackData->feedbacks()->create(['participant_id' => $course->participants()->first()->id])->id;
    }

    protected function fakeDNSValidation() {
        $this->app->bind(DNSCheckValidation::class, TestDNSCheckValidation::class);
    }
}

class TestDNSCheckValidation extends DNSCheckValidation {
    /**
     * In this test, we want the DNS validation to fail for fail.com domains, and pass for all others.
     */
    public function isValid(string $email, EmailLexer $emailLexer): bool {
        return !str_ends_with($email, 'fail.com');
    }
}
