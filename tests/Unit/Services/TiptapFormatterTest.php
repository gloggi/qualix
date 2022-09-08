<?php

namespace Tests\Unit\Services;

use App\Exceptions\RequirementsMismatchException;
use App\Models\Block;
use App\Models\Course;
use App\Models\Feedback;
use App\Models\FeedbackData;
use App\Models\Observation;
use App\Models\ParticipantObservation;
use App\Services\TiptapFormatter;
use Tests\TestCase;

class TiptapFormatterTest extends TestCase {

    public function test_toTiptap_shouldWork_withEmptyFeedback() {
        // given
        $feedback = $this->createFeedback();
        $formatter = new TiptapFormatter($feedback);

        // when
        $result = $formatter->toTiptap();

        // then
        $this->assertEquals(json_encode(['type' => 'doc', 'content' => []]), json_encode($result));
    }

    public function test_toTiptap_shouldWork_withDefaultDocument() {
        // given
        $feedback = $this->createFeedback();
        $feedback->contentNodes()->create(['json' => json_encode(['type' => 'paragraph']), 'order' => 0]);
        $formatter = new TiptapFormatter($feedback);

        // when
        $result = $formatter->toTiptap();

        // then
        $this->assertEquals(json_encode(['type' => 'doc', 'content' => [
            ['type' => 'paragraph'],
        ]]), json_encode($result));
    }

    public function test_toTiptap_shouldWork_withTextContent() {
        // given
        $feedback = $this->createFeedback();
        $feedback->contentNodes()->create(['json' => json_encode(['type' => 'paragraph']), 'order' => 0]);
        $feedback->contentNodes()->create(['json' => json_encode(['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]]), 'order' => 1]);
        $feedback->contentNodes()->create(['json' => json_encode(['type' => 'heading', 'attrs' => ['level' => 5], 'content' => [['type' => 'text', 'text' => 'hello']]]), 'order' => 2]);
        $formatter = new TiptapFormatter($feedback);

        // when
        $result = $formatter->toTiptap();

        // then
        $this->assertEquals(json_encode(['type' => 'doc', 'content' => [
            ['type' => 'paragraph'],
            ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]],
            ['type' => 'heading', 'attrs' => ['level' => 5], 'content' => [['type' => 'text', 'text' => 'hello']]],
        ]]), json_encode($result));
    }

    public function test_toTiptap_shouldWork_withObservationContent() {
        // given
        $feedback = $this->createFeedback();
        $course = $feedback->participant->course;
        $participantObservation = $this->createParticipantObservation($course, $feedback);
        $feedback->participant_observations()->attach([$participantObservation->id => ['order' => 0]]);
        $formatter = new TiptapFormatter($feedback);

        // when
        $result = $formatter->toTiptap();

        // then
        $this->assertEquals(json_encode(['type' => 'doc', 'content' => [
            ['type' => 'observation', 'attrs' => ['id' => $participantObservation->id]],
        ]]), json_encode($result));
    }

    public function test_toTiptap_shouldWork_withRequirementContent() {
        // given
        $feedback = $this->createFeedback();
        $course = $feedback->participant->course;
        $requirementStatusId = $course->default_requirement_status_id;
        $requirement = $course->requirements()->first();
        $feedback->feedback_requirements()->create(['requirement' => $requirement->id, 'order' => 0, 'requirement_status' => $requirementStatusId]);
        $formatter = new TiptapFormatter($feedback->fresh());

        // when
        $result = $formatter->toTiptap();

        // then
        $this->assertEquals(json_encode(['type' => 'doc', 'content' => [
            ['type' => 'requirement', 'attrs' => ['id' => $requirement->id, 'status_id' => $requirementStatusId, 'comment' => '']],
        ]]), json_encode($result));
    }

    public function test_toTiptap_shouldSortContents_byOrderField() {
        // given
        $feedback = $this->createFeedback();
        $course = $feedback->participant->course;
        $requirementStatusId = $course->default_requirement_status_id;

        $feedback->contentNodes()->create(['json' => json_encode(['type' => 'paragraph']), 'order' => 1]);
        $feedback->contentNodes()->create(['json' => json_encode(['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]]), 'order' => 4]);
        $feedback->contentNodes()->create(['json' => json_encode(['type' => 'heading', 'attrs' => ['level' => 5], 'content' => [['type' => 'text', 'text' => 'hello']]]), 'order' => 0]);

        $participantObservation = $this->createParticipantObservation($course, $feedback);
        $feedback->participant_observations()->attach([$participantObservation->id => ['order' => 2]]);

        $requirement = $course->requirements()->first();
        $feedback->feedback_requirements()->create(['requirement' => $requirement->id, 'order' => 3, 'requirement_status' => $requirementStatusId]);

        $formatter = new TiptapFormatter($feedback->fresh());

        // when
        $result = $formatter->toTiptap();

        // then
        $this->assertEquals(json_encode(['type' => 'doc', 'content' => [
            ['type' => 'heading', 'attrs' => ['level' => 5], 'content' => [['type' => 'text', 'text' => 'hello']]],
            ['type' => 'paragraph'],
            ['type' => 'observation', 'attrs' => ['id' => $participantObservation->id]],
            ['type' => 'requirement', 'attrs' => ['id' => $requirement->id, 'status_id' => $requirementStatusId, 'comment' => '']],
            ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]],
        ]]), json_encode($result));
    }

    /**
     * @throws \App\Exceptions\RequirementsMismatchException
     */
    public function test_applyToFeedback_shouldApplyEmpty() {
        // given
        $feedback = $this->createFeedback();
        $formatter = new TiptapFormatter($feedback);

        // when
        $formatter->applyToFeedback(['type' => 'doc', 'content' => []]);

        // then
        $feedback = Feedback::find($feedback->id);
        $this->assertEquals(0, $feedback->contentNodes()->count());
        $this->assertEquals(0, $feedback->participant_observations()->count());
        $this->assertEquals(0, $feedback->requirements()->count());
    }

    /**
     * @throws \App\Exceptions\RequirementsMismatchException
     */
    public function test_applyToFeedback_shouldApplyDefaultDocument() {
        // given
        $feedback = $this->createFeedback();
        $formatter = new TiptapFormatter($feedback);

        // when
        $formatter->applyToFeedback(['type' => 'doc', 'content' => [['type' => 'paragraph']]]);

        // then
        $feedback = Feedback::find($feedback->id);
        $this->assertEquals(1, $feedback->contentNodes()->count());
        $this->assertEquals(0, $feedback->participant_observations()->count());
        $this->assertEquals(0, $feedback->requirements()->count());
        $paragraph = $feedback->contentNodes()->first();
        $this->assertEquals(json_encode(['type' => 'paragraph']), $paragraph->json);
        $this->assertEquals(0, $paragraph->order);
    }

    /**
     * @throws \App\Exceptions\RequirementsMismatchException
     */
    public function test_applyToFeedback_shouldApplyTextNodes() {
        // given
        $feedback = $this->createFeedback();
        $formatter = new TiptapFormatter($feedback);

        // when
        $formatter->applyToFeedback(['type' => 'doc', 'content' => [
            ['type' => 'paragraph'],
            ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]],
            ['type' => 'heading', 'attrs' => ['level' => 5], 'content' => [['type' => 'text', 'text' => 'hello']]],
        ]]);

        // then
        $feedback = Feedback::find($feedback->id);
        $this->assertEquals(3, $feedback->contentNodes()->count());
        $this->assertEquals(0, $feedback->participant_observations()->count());
        $this->assertEquals(0, $feedback->requirements()->count());
        $nodes = $feedback->contentNodes;
        $this->assertEquals(json_encode(['type' => 'paragraph']), $nodes[0]->json);
        $this->assertEquals(0, $nodes[0]->order);
        $this->assertEquals(json_encode(['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]]), $nodes[1]->json);
        $this->assertEquals(1, $nodes[1]->order);
        $this->assertEquals(json_encode(['type' => 'heading', 'attrs' => ['level' => 5], 'content' => [['type' => 'text', 'text' => 'hello']]]), $nodes[2]->json);
        $this->assertEquals(2, $nodes[2]->order);
    }

    /**
     * @throws \App\Exceptions\RequirementsMismatchException
     */
    public function test_applyToFeedback_shouldApplyObservationNode() {
        // given
        $feedback = $this->createFeedback();
        $course = $feedback->participant->course;
        $participantObservation = $this->createParticipantObservation($course, $feedback);
        $formatter = new TiptapFormatter($feedback);

        // when
        $formatter->applyToFeedback(['type' => 'doc', 'content' => [
            ['type' => 'observation', 'attrs' => ['id' => $participantObservation->id]],
        ]]);

        // then
        $feedback = Feedback::find($feedback->id);
        $this->assertEquals(0, $feedback->contentNodes()->count());
        $this->assertEquals(1, $feedback->participant_observations()->count());
        $this->assertEquals(0, $feedback->requirements()->count());
        $observation = $feedback->participant_observations()->withPivot(['order'])->first();
        $this->assertEquals($participantObservation->id, $observation->id);
        $this->assertEquals(0, $observation->pivot->order);
    }

    /**
     * @throws \App\Exceptions\RequirementsMismatchException
     */
    public function test_applyToFeedback_shouldApplyRequirementNode() {
        // given
        $feedback = $this->createFeedback();
        $course = $feedback->participant->course;
        $requirementStatusId = $course->default_requirement_status_id;
        $requirement = $course->requirements()->first();
        $formatter = new TiptapFormatter($feedback);

        // when
        $formatter->applyToFeedback(['type' => 'doc', 'content' => [
            ['type' => 'requirement', 'attrs' => ['id' => $requirement->id, 'status_id' => $requirementStatusId]],
        ]], false);

        // then
        $feedback = Feedback::find($feedback->id);
        $this->assertEquals(0, $feedback->contentNodes()->count());
        $this->assertEquals(0, $feedback->participant_observations()->count());
        $this->assertEquals(1, $feedback->requirements()->count());
        $feedbackRequirement = $feedback->requirements()->first();
        $this->assertEquals($requirement->id, $feedbackRequirement->id);
        $this->assertEquals(0, $feedbackRequirement->order);
        $this->assertEquals($requirementStatusId, $feedbackRequirement->status_id);
    }

    /**
     */
    public function test_applyToFeedback_shouldThrow_whenApplyRequirementNode_andCheckingRequirements() {
        // given
        $feedback = $this->createFeedback();
        $course = $feedback->participant->course;
        $requirement = $course->requirements()->first();
        $formatter = new TiptapFormatter($feedback);

        // when
        try {
            $formatter->applyToFeedback(['type' => 'doc', 'content' => [
                ['type' => 'requirement', 'attrs' => ['id' => $requirement->id, 'status_id' => 1]],
            ]], true);

            $this->fail('expected RequirementsOutdatedException to be thrown');
        }

        // then
        catch(RequirementsMismatchException $e) {
            $feedback = Feedback::find($feedback->id);
            $this->assertEquals(0, $feedback->contentNodes()->count());
            $this->assertEquals(0, $feedback->participant_observations()->count());
            $this->assertEquals(0, $feedback->requirements()->count());
        }
    }

    /**
     * @throws \App\Exceptions\RequirementsMismatchException
     */
    public function test_applyToFeedback_shouldApplyMixedNodes() {
        // given
        $feedback = $this->createFeedback();
        $course = $feedback->participant->course;
        $requirementStatusId = $course->default_requirement_status_id;
        $participantObservation = $this->createParticipantObservation($course, $feedback);
        $requirement = $course->requirements()->first();
        $formatter = new TiptapFormatter($feedback);

        // when
        $formatter->applyToFeedback(['type' => 'doc', 'content' => [
            ['type' => 'heading', 'attrs' => ['level' => 5], 'content' => [['type' => 'text', 'text' => 'hello']]],
            ['type' => 'paragraph'],
            ['type' => 'observation', 'attrs' => ['id' => $participantObservation->id]],
            ['type' => 'requirement', 'attrs' => ['id' => $requirement->id, 'status_id' => $requirementStatusId]],
            ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]],
        ]], false);

        // then
        $feedback = Feedback::find($feedback->id);
        $this->assertEquals(3, $feedback->contentNodes()->count());
        $this->assertEquals(1, $feedback->participant_observations()->count());
        $this->assertEquals(1, $feedback->requirements()->count());
        $nodes = $feedback->contentNodes;
        $this->assertEquals(json_encode(['type' => 'heading', 'attrs' => ['level' => 5], 'content' => [['type' => 'text', 'text' => 'hello']]]), $nodes[0]->json);
        $this->assertEquals(0, $nodes[0]->order);
        $this->assertEquals(json_encode(['type' => 'paragraph']), $nodes[1]->json);
        $this->assertEquals(1, $nodes[1]->order);
        $this->assertEquals(json_encode(['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]]), $nodes[2]->json);
        $this->assertEquals(4, $nodes[2]->order);
        $feedbackRequirement = $feedback->requirements()->first();
        $this->assertEquals($requirement->id, $feedbackRequirement->id);
        $this->assertEquals(3, $feedbackRequirement->order);
        $this->assertEquals($requirementStatusId, $feedbackRequirement->status_id);
        $observation = $feedback->participant_observations()->withPivot(['order'])->first();
        $this->assertEquals($participantObservation->id, $observation->id);
        $this->assertEquals(2, $observation->pivot->order);
    }

    /**
     * @throws \App\Exceptions\RequirementsMismatchException
     */
    public function test_applyToFeedback_shouldReorderNodes() {
        // given
        $feedback = $this->createFeedback();
        $course = $feedback->participant->course;
        $requirementStatusId = $course->default_requirement_status_id;
        $requirementStatusId2 = $course->requirement_statuses()->pluck('id')->last();
        $participantObservation = $this->createParticipantObservation($course, $feedback);
        $requirement = $course->requirements()->first();
        $formatter = new TiptapFormatter($feedback);
        $formatter->applyToFeedback(['type' => 'doc', 'content' => [
            ['type' => 'heading', 'attrs' => ['level' => 5], 'content' => [['type' => 'text', 'text' => 'hello']]],
            ['type' => 'paragraph'],
            ['type' => 'observation', 'attrs' => ['id' => $participantObservation->id]],
            ['type' => 'requirement', 'attrs' => ['id' => $requirement->id, 'status_id' => $requirementStatusId2]],
            ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]],
        ]], false);

        // when
        $formatter->applyToFeedback(['type' => 'doc', 'content' => [
            ['type' => 'observation', 'attrs' => ['id' => $participantObservation->id]],
            ['type' => 'requirement', 'attrs' => ['id' => $requirement->id, 'status_id' => $requirementStatusId]],
            ['type' => 'paragraph'],
            ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]],
            ['type' => 'heading', 'attrs' => ['level' => 5], 'content' => [['type' => 'text', 'text' => 'hello']]],
        ]]);

        // then
        $feedback = Feedback::find($feedback->id);
        $this->assertEquals(3, $feedback->contentNodes()->count());
        $this->assertEquals(1, $feedback->participant_observations()->count());
        $this->assertEquals(1, $feedback->requirements()->count());
        $nodes = $feedback->contentNodes;
        $this->assertEquals(json_encode(['type' => 'paragraph']), $nodes[0]->json);
        $this->assertEquals(2, $nodes[0]->order);
        $this->assertEquals(json_encode(['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]]), $nodes[1]->json);
        $this->assertEquals(3, $nodes[1]->order);
        $this->assertEquals(json_encode(['type' => 'heading', 'attrs' => ['level' => 5], 'content' => [['type' => 'text', 'text' => 'hello']]]), $nodes[2]->json);
        $this->assertEquals(4, $nodes[2]->order);
        $feedbackRequirement = $feedback->requirements()->first();
        $this->assertEquals($requirement->id, $feedbackRequirement->id);
        $this->assertEquals(1, $feedbackRequirement->order);
        $this->assertEquals($requirementStatusId, $feedbackRequirement->status_id);
        $observation = $feedback->participant_observations()->withPivot(['order'])->first();
        $this->assertEquals($participantObservation->id, $observation->id);
        $this->assertEquals(0, $observation->pivot->order);
    }

    /**
     * @throws \App\Exceptions\RequirementsMismatchException
     */
    public function test_appendRequirementsToFeedback_shouldWork_andAppendAnEmptyParagraphAfterEachAppendedRequirement() {
        // given
        $feedback = $this->createFeedback();
        $course = $feedback->participant->course;
        $participantObservation = $this->createParticipantObservation($course, $feedback);
        $requirements = $course->requirements;
        $requirement = $requirements[0];
        $requirement2 = $requirements[1];
        $requirement3 = $requirements[2];
        $requirementStatusId = $course->default_requirement_status_id;
        $requirementStatusId2 = $course->requirement_statuses()->pluck('id')->last();
        $formatter = new TiptapFormatter($feedback);
        $formatter->applyToFeedback(['type' => 'doc', 'content' => [
            ['type' => 'heading', 'attrs' => ['level' => 5], 'content' => [['type' => 'text', 'text' => 'hello']]],
            ['type' => 'paragraph'],
            ['type' => 'observation', 'attrs' => ['id' => $participantObservation->id]],
            ['type' => 'requirement', 'attrs' => ['id' => $requirement->id, 'status_id' => $requirementStatusId2]],
            ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]],
        ]], false);

        // when
        $formatter->appendRequirementsToFeedback(collect([$requirement2, $requirement3]));

        // then
        $feedback = Feedback::find($feedback->id);
        $this->assertEquals(5, $feedback->contentNodes()->count());
        $this->assertEquals(1, $feedback->participant_observations()->count());
        $this->assertEquals(3, $feedback->requirements()->count());
        $nodes = $feedback->contentNodes;
        $this->assertEquals(json_encode(['type' => 'heading', 'attrs' => ['level' => 5], 'content' => [['type' => 'text', 'text' => 'hello']]]), $nodes[0]->json);
        $this->assertEquals(0, $nodes[0]->order);
        $this->assertEquals(json_encode(['type' => 'paragraph']), $nodes[1]->json);
        $this->assertEquals(1, $nodes[1]->order);
        $this->assertEquals(json_encode(['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]]), $nodes[2]->json);
        $this->assertEquals(4, $nodes[2]->order);
        $this->assertEquals(json_encode(['type' => 'paragraph']), $nodes[3]->json);
        $this->assertEquals(6, $nodes[3]->order);
        $this->assertEquals(json_encode(['type' => 'paragraph']), $nodes[4]->json);
        $this->assertEquals(8, $nodes[4]->order);
        [$feedbackRequirement, $feedbackRequirement2, $feedbackRequirement3] = $feedback->requirements()->get();
        $this->assertEquals($requirement->id, $feedbackRequirement->id);
        $this->assertEquals(3, $feedbackRequirement->order);
        $this->assertEquals($requirementStatusId2, $feedbackRequirement->status_id);
        $this->assertEquals($requirement2->id, $feedbackRequirement2->id);
        $this->assertEquals(5, $feedbackRequirement2->order);
        $this->assertEquals($requirementStatusId, $feedbackRequirement2->status_id);
        $this->assertEquals($requirement3->id, $feedbackRequirement3->id);
        $this->assertEquals(7, $feedbackRequirement3->order);
        $this->assertEquals($requirementStatusId, $feedbackRequirement3->status_id);
        $observation = $feedback->participant_observations()->withPivot(['order'])->first();
        $this->assertEquals($participantObservation->id, $observation->id);
        $this->assertEquals(2, $observation->pivot->order);
    }

    /**
     * @throws \App\Exceptions\RequirementsMismatchException
     */
    public function test_appendRequirementsToFeedback_shouldDoNothing_whenPassedSomethingOtherThanRequirements() {
        // given
        $feedback = $this->createFeedback();
        $course = $feedback->participant->course;
        $participantObservation = $this->createParticipantObservation($course, $feedback);
        $requirements = $course->requirements;
        $requirement = $requirements[0];
        $requirement2 = $requirements[1];
        $requirementStatusId = $course->default_requirement_status_id;
        $requirementStatusId2 = $course->requirement_statuses()->pluck('id')->last();
        $formatter = new TiptapFormatter($feedback);
        $formatter->applyToFeedback(['type' => 'doc', 'content' => [
            ['type' => 'heading', 'attrs' => ['level' => 5], 'content' => [['type' => 'text', 'text' => 'hello']]],
            ['type' => 'paragraph'],
            ['type' => 'observation', 'attrs' => ['id' => $participantObservation->id]],
            ['type' => 'requirement', 'attrs' => ['id' => $requirement->id, 'status_id' => $requirementStatusId2]],
            ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]],
        ]], false);

        // when
        $formatter->appendRequirementsToFeedback(collect([$requirement2, $participantObservation]));

        // then
        $feedback = Feedback::find($feedback->id);
        $this->assertEquals(4, $feedback->contentNodes()->count());
        $this->assertEquals(1, $feedback->participant_observations()->count());
        $this->assertEquals(2, $feedback->requirements()->count());
        $nodes = $feedback->contentNodes;
        $this->assertEquals(json_encode(['type' => 'heading', 'attrs' => ['level' => 5], 'content' => [['type' => 'text', 'text' => 'hello']]]), $nodes[0]->json);
        $this->assertEquals(0, $nodes[0]->order);
        $this->assertEquals(json_encode(['type' => 'paragraph']), $nodes[1]->json);
        $this->assertEquals(1, $nodes[1]->order);
        $this->assertEquals(json_encode(['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]]), $nodes[2]->json);
        $this->assertEquals(4, $nodes[2]->order);
        $this->assertEquals(json_encode(['type' => 'paragraph']), $nodes[3]->json);
        $this->assertEquals(6, $nodes[3]->order);
        [$feedbackRequirement, $feedbackRequirement2] = $feedback->requirements()->get();
        $this->assertEquals($requirement->id, $feedbackRequirement->id);
        $this->assertEquals(3, $feedbackRequirement->order);
        $this->assertEquals($requirementStatusId2, $feedbackRequirement->status_id);
        $this->assertEquals($requirement2->id, $feedbackRequirement2->id);
        $this->assertEquals(5, $feedbackRequirement2->order);
        $this->assertEquals($requirementStatusId, $feedbackRequirement2->status_id);
        $observation = $feedback->participant_observations()->withPivot(['order'])->first();
        $this->assertEquals($participantObservation->id, $observation->id);
        $this->assertEquals(2, $observation->pivot->order);
    }

    /**
     * @return Feedback
     */
    protected function createFeedback() {
        /** @var Course $course */
        $course = Course::factory()
            ->hasUsers(3)
            ->hasRequirements(4)
            ->hasRequirementStatuses(3)
            ->hasParticipants(10)
            ->has(Block::factory()
                ->count(10)
                ->has(Observation::factory()
                    ->count(5)
                    ->fromRandomUser()
                    ->withRequirements()
                    ->maybeMultiParticipant()
                )
            )
            ->has(FeedbackData::factory()
                ->has(Feedback::factory()
                    ->forParticipants()
                ), 'feedback_datas'
            )
            ->create();
        return $course->feedbacks()->first();
    }

    /**
     * @param Course $course
     * @param Feedback $feedback
     * @return ParticipantObservation
     */
    public function createParticipantObservation(Course $course, Feedback $feedback) {
        /** @var Observation $observation */
        $observation = Observation::create(['content' => 'hat gut aufgepasst', 'impression' => 1, 'block' => $course->blocks()->first(), 'user_id' => $course->users()->first()->id]);
        $feedback->participant->observations()->attach($observation);
        /** @var ParticipantObservation $participantObservation */
        $participantObservation = $feedback->participant->participant_observations()->first();
        return $participantObservation;
    }
}
