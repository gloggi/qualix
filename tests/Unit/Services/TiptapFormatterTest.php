<?php

namespace Tests\Unit\Services;

use App\Exceptions\RequirementsOutdatedException;
use App\Models\Course;
use App\Models\Observation;
use App\Models\ParticipantObservation;
use App\Models\Quali;
use App\Models\QualiData;
use App\Services\TiptapFormatter;
use Tests\TestCase;

class TiptapFormatterTest extends TestCase {

    public function test_toTiptap_shouldWork_withEmptyQuali() {
        // given
        $quali = $this->createQuali();
        $formatter = new TiptapFormatter($quali);

        // when
        $result = $formatter->toTiptap();

        // then
        $this->assertEquals(json_encode(['type' => 'doc', 'content' => []]), json_encode($result));
    }

    public function test_toTiptap_shouldWork_withDefaultDocument() {
        // given
        $quali = $this->createQuali();
        $quali->contentNodes()->create(['json' => json_encode(['type' => 'paragraph']), 'order' => 0]);
        $formatter = new TiptapFormatter($quali);

        // when
        $result = $formatter->toTiptap();

        // then
        $this->assertEquals(json_encode(['type' => 'doc', 'content' => [
            ['type' => 'paragraph'],
        ]]), json_encode($result));
    }

    public function test_toTiptap_shouldWork_withTextContent() {
        // given
        $quali = $this->createQuali();
        $quali->contentNodes()->create(['json' => json_encode(['type' => 'paragraph']), 'order' => 0]);
        $quali->contentNodes()->create(['json' => json_encode(['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]]), 'order' => 1]);
        $quali->contentNodes()->create(['json' => json_encode(['type' => 'heading', 'attrs' => ['level' => 5], 'content' => [['type' => 'text', 'text' => 'hello']]]), 'order' => 2]);
        $formatter = new TiptapFormatter($quali);

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
        $quali = $this->createQuali();
        $course = $quali->participant->course;
        $participantObservation = $this->createParticipantObservation($course, $quali);
        $quali->participant_observations()->attach([$participantObservation->id => ['order' => 0]]);
        $formatter = new TiptapFormatter($quali);

        // when
        $result = $formatter->toTiptap();

        // then
        $this->assertEquals(json_encode(['type' => 'doc', 'content' => [
            ['type' => 'observation', 'attrs' => ['id' => $participantObservation->id]],
        ]]), json_encode($result));
    }

    public function test_toTiptap_shouldWork_withRequirementContent() {
        // given
        $quali = $this->createQuali();
        $course = $quali->participant->course;
        $requirement = $course->requirements()->first();
        $quali->requirements()->attach([$requirement->id => ['order' => 0, 'passed' => 1]]);
        $formatter = new TiptapFormatter($quali);

        // when
        $result = $formatter->toTiptap();

        // then
        $this->assertEquals(json_encode(['type' => 'doc', 'content' => [
            ['type' => 'requirement', 'attrs' => ['id' => $requirement->id, 'passed' => 1]],
        ]]), json_encode($result));
    }

    public function test_toTiptap_shouldSortContents_byOrderField() {
        // given
        $quali = $this->createQuali();
        $course = $quali->participant->course;

        $quali->contentNodes()->create(['json' => json_encode(['type' => 'paragraph']), 'order' => 1]);
        $quali->contentNodes()->create(['json' => json_encode(['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]]), 'order' => 4]);
        $quali->contentNodes()->create(['json' => json_encode(['type' => 'heading', 'attrs' => ['level' => 5], 'content' => [['type' => 'text', 'text' => 'hello']]]), 'order' => 0]);

        $participantObservation = $this->createParticipantObservation($course, $quali);
        $quali->participant_observations()->attach([$participantObservation->id => ['order' => 2]]);

        $requirement = $course->requirements()->first();
        $quali->requirements()->attach([$requirement->id => ['order' => 3, 'passed' => 0]]);

        $formatter = new TiptapFormatter($quali);

        // when
        $result = $formatter->toTiptap();

        // then
        $this->assertEquals(json_encode(['type' => 'doc', 'content' => [
            ['type' => 'heading', 'attrs' => ['level' => 5], 'content' => [['type' => 'text', 'text' => 'hello']]],
            ['type' => 'paragraph'],
            ['type' => 'observation', 'attrs' => ['id' => $participantObservation->id]],
            ['type' => 'requirement', 'attrs' => ['id' => $requirement->id, 'passed' => 0]],
            ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]],
        ]]), json_encode($result));
    }

    /**
     * @throws \App\Exceptions\RequirementsOutdatedException
     */
    public function test_applyToQuali_shouldApplyEmpty() {
        // given
        $quali = $this->createQuali();
        $formatter = new TiptapFormatter($quali);

        // when
        $formatter->applyToQuali(['type' => 'doc', 'content' => []]);

        // then
        $quali = Quali::find($quali->id);
        $this->assertEquals(0, $quali->contentNodes()->count());
        $this->assertEquals(0, $quali->participant_observations()->count());
        $this->assertEquals(0, $quali->requirements()->count());
    }

    /**
     * @throws \App\Exceptions\RequirementsOutdatedException
     */
    public function test_applyToQuali_shouldApplyDefaultDocument() {
        // given
        $quali = $this->createQuali();
        $formatter = new TiptapFormatter($quali);

        // when
        $formatter->applyToQuali(['type' => 'doc', 'content' => [['type' => 'paragraph']]]);

        // then
        $quali = Quali::find($quali->id);
        $this->assertEquals(1, $quali->contentNodes()->count());
        $this->assertEquals(0, $quali->participant_observations()->count());
        $this->assertEquals(0, $quali->requirements()->count());
        $paragraph = $quali->contentNodes()->first();
        $this->assertEquals(json_encode(['type' => 'paragraph']), $paragraph->json);
        $this->assertEquals(0, $paragraph->order);
    }

    /**
     * @throws \App\Exceptions\RequirementsOutdatedException
     */
    public function test_applyToQuali_shouldApplyTextNodes() {
        // given
        $quali = $this->createQuali();
        $formatter = new TiptapFormatter($quali);

        // when
        $formatter->applyToQuali(['type' => 'doc', 'content' => [
            ['type' => 'paragraph'],
            ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]],
            ['type' => 'heading', 'attrs' => ['level' => 5], 'content' => [['type' => 'text', 'text' => 'hello']]],
        ]]);

        // then
        $quali = Quali::find($quali->id);
        $this->assertEquals(3, $quali->contentNodes()->count());
        $this->assertEquals(0, $quali->participant_observations()->count());
        $this->assertEquals(0, $quali->requirements()->count());
        $nodes = $quali->contentNodes;
        $this->assertEquals(json_encode(['type' => 'paragraph']), $nodes[0]->json);
        $this->assertEquals(0, $nodes[0]->order);
        $this->assertEquals(json_encode(['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]]), $nodes[1]->json);
        $this->assertEquals(1, $nodes[1]->order);
        $this->assertEquals(json_encode(['type' => 'heading', 'attrs' => ['level' => 5], 'content' => [['type' => 'text', 'text' => 'hello']]]), $nodes[2]->json);
        $this->assertEquals(2, $nodes[2]->order);
    }

    /**
     * @throws \App\Exceptions\RequirementsOutdatedException
     */
    public function test_applyToQuali_shouldApplyObservationNode() {
        // given
        $quali = $this->createQuali();
        $course = $quali->participant->course;
        $participantObservation = $this->createParticipantObservation($course, $quali);
        $formatter = new TiptapFormatter($quali);

        // when
        $formatter->applyToQuali(['type' => 'doc', 'content' => [
            ['type' => 'observation', 'attrs' => ['id' => $participantObservation->id]],
        ]]);

        // then
        $quali = Quali::find($quali->id);
        $this->assertEquals(0, $quali->contentNodes()->count());
        $this->assertEquals(1, $quali->participant_observations()->count());
        $this->assertEquals(0, $quali->requirements()->count());
        $observation = $quali->participant_observations()->withPivot(['order'])->first();
        $this->assertEquals($participantObservation->id, $observation->id);
        $this->assertEquals(0, $observation->pivot->order);
    }

    /**
     * @throws \App\Exceptions\RequirementsOutdatedException
     */
    public function test_applyToQuali_shouldApplyRequirementNode() {
        // given
        $quali = $this->createQuali();
        $course = $quali->participant->course;
        $requirement = $course->requirements()->first();
        $formatter = new TiptapFormatter($quali);

        // when
        $formatter->applyToQuali(['type' => 'doc', 'content' => [
            ['type' => 'requirement', 'attrs' => ['id' => $requirement->id, 'passed' => 1]],
        ]], false);

        // then
        $quali = Quali::find($quali->id);
        $this->assertEquals(0, $quali->contentNodes()->count());
        $this->assertEquals(0, $quali->participant_observations()->count());
        $this->assertEquals(1, $quali->requirements()->count());
        $qualiRequirement = $quali->requirements()->withPivot(['order', 'passed'])->first();
        $this->assertEquals($requirement->id, $qualiRequirement->id);
        $this->assertEquals(0, $qualiRequirement->pivot->order);
        $this->assertEquals(1, $qualiRequirement->pivot->passed);
    }

    /**
     */
    public function test_applyToQuali_shouldThrow_whenApplyRequirementNode_andCheckingRequirements() {
        // given
        $quali = $this->createQuali();
        $course = $quali->participant->course;
        $requirement = $course->requirements()->first();
        $formatter = new TiptapFormatter($quali);

        // when
        try {
            $formatter->applyToQuali(['type' => 'doc', 'content' => [
                ['type' => 'requirement', 'attrs' => ['id' => $requirement->id, 'passed' => 1]],
            ]], true);

            $this->fail('expected RequirementsOutdatedException to be thrown');
        }

        // then
        catch(RequirementsOutdatedException $e) {
            $quali = Quali::find($quali->id);
            $this->assertEquals(0, $quali->contentNodes()->count());
            $this->assertEquals(0, $quali->participant_observations()->count());
            $this->assertEquals(0, $quali->requirements()->count());
        }
    }

    /**
     * @throws \App\Exceptions\RequirementsOutdatedException
     */
    public function test_applyToQuali_shouldApplyMixedNodes() {
        // given
        $quali = $this->createQuali();
        $course = $quali->participant->course;
        $participantObservation = $this->createParticipantObservation($course, $quali);
        $requirement = $course->requirements()->first();
        $formatter = new TiptapFormatter($quali);

        // when
        $formatter->applyToQuali(['type' => 'doc', 'content' => [
            ['type' => 'heading', 'attrs' => ['level' => 5], 'content' => [['type' => 'text', 'text' => 'hello']]],
            ['type' => 'paragraph'],
            ['type' => 'observation', 'attrs' => ['id' => $participantObservation->id]],
            ['type' => 'requirement', 'attrs' => ['id' => $requirement->id, 'passed' => null]],
            ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]],
        ]], false);

        // then
        $quali = Quali::find($quali->id);
        $this->assertEquals(3, $quali->contentNodes()->count());
        $this->assertEquals(1, $quali->participant_observations()->count());
        $this->assertEquals(1, $quali->requirements()->count());
        $nodes = $quali->contentNodes;
        $this->assertEquals(json_encode(['type' => 'heading', 'attrs' => ['level' => 5], 'content' => [['type' => 'text', 'text' => 'hello']]]), $nodes[0]->json);
        $this->assertEquals(0, $nodes[0]->order);
        $this->assertEquals(json_encode(['type' => 'paragraph']), $nodes[1]->json);
        $this->assertEquals(1, $nodes[1]->order);
        $this->assertEquals(json_encode(['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]]), $nodes[2]->json);
        $this->assertEquals(4, $nodes[2]->order);
        $qualiRequirement = $quali->requirements()->withPivot(['order', 'passed'])->first();
        $this->assertEquals($requirement->id, $qualiRequirement->id);
        $this->assertEquals(3, $qualiRequirement->pivot->order);
        $this->assertEquals(null, $qualiRequirement->pivot->passed);
        $observation = $quali->participant_observations()->withPivot(['order'])->first();
        $this->assertEquals($participantObservation->id, $observation->id);
        $this->assertEquals(2, $observation->pivot->order);
    }

    /**
     * @throws \App\Exceptions\RequirementsOutdatedException
     */
    public function test_applyToQuali_shouldReorderNodes() {
        // given
        $quali = $this->createQuali();
        $course = $quali->participant->course;
        $participantObservation = $this->createParticipantObservation($course, $quali);
        $requirement = $course->requirements()->first();
        $formatter = new TiptapFormatter($quali);
        $formatter->applyToQuali(['type' => 'doc', 'content' => [
            ['type' => 'heading', 'attrs' => ['level' => 5], 'content' => [['type' => 'text', 'text' => 'hello']]],
            ['type' => 'paragraph'],
            ['type' => 'observation', 'attrs' => ['id' => $participantObservation->id]],
            ['type' => 'requirement', 'attrs' => ['id' => $requirement->id, 'passed' => null]],
            ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]],
        ]], false);

        // when
        $formatter->applyToQuali(['type' => 'doc', 'content' => [
            ['type' => 'observation', 'attrs' => ['id' => $participantObservation->id]],
            ['type' => 'requirement', 'attrs' => ['id' => $requirement->id, 'passed' => 0]],
            ['type' => 'paragraph'],
            ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]],
            ['type' => 'heading', 'attrs' => ['level' => 5], 'content' => [['type' => 'text', 'text' => 'hello']]],
        ]]);

        // then
        $quali = Quali::find($quali->id);
        $this->assertEquals(3, $quali->contentNodes()->count());
        $this->assertEquals(1, $quali->participant_observations()->count());
        $this->assertEquals(1, $quali->requirements()->count());
        $nodes = $quali->contentNodes;
        $this->assertEquals(json_encode(['type' => 'paragraph']), $nodes[0]->json);
        $this->assertEquals(2, $nodes[0]->order);
        $this->assertEquals(json_encode(['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]]), $nodes[1]->json);
        $this->assertEquals(3, $nodes[1]->order);
        $this->assertEquals(json_encode(['type' => 'heading', 'attrs' => ['level' => 5], 'content' => [['type' => 'text', 'text' => 'hello']]]), $nodes[2]->json);
        $this->assertEquals(4, $nodes[2]->order);
        $qualiRequirement = $quali->requirements()->withPivot(['order', 'passed'])->first();
        $this->assertEquals($requirement->id, $qualiRequirement->id);
        $this->assertEquals(1, $qualiRequirement->pivot->order);
        $this->assertEquals(null, $qualiRequirement->pivot->passed);
        $observation = $quali->participant_observations()->withPivot(['order'])->first();
        $this->assertEquals($participantObservation->id, $observation->id);
        $this->assertEquals(0, $observation->pivot->order);
    }

    /**
     * @throws \App\Exceptions\RequirementsOutdatedException
     */
    public function test_appendRequirementsToQuali_shouldWork_andAppendAnEmptyParagraphAfterEachAppendedRequirement() {
        // given
        $quali = $this->createQuali();
        $course = $quali->participant->course;
        $participantObservation = $this->createParticipantObservation($course, $quali);
        $requirements = $course->requirements;
        $requirement = $requirements[0];
        $requirement2 = $requirements[1];
        $requirement3 = $requirements[2];
        $formatter = new TiptapFormatter($quali);
        $formatter->applyToQuali(['type' => 'doc', 'content' => [
            ['type' => 'heading', 'attrs' => ['level' => 5], 'content' => [['type' => 'text', 'text' => 'hello']]],
            ['type' => 'paragraph'],
            ['type' => 'observation', 'attrs' => ['id' => $participantObservation->id]],
            ['type' => 'requirement', 'attrs' => ['id' => $requirement->id, 'passed' => 1]],
            ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]],
        ]], false);

        // when
        $formatter->appendRequirementsToQuali(collect([$requirement2, $requirement3]));

        // then
        $quali = Quali::find($quali->id);
        $this->assertEquals(5, $quali->contentNodes()->count());
        $this->assertEquals(1, $quali->participant_observations()->count());
        $this->assertEquals(3, $quali->requirements()->count());
        $nodes = $quali->contentNodes;
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
        [$qualiRequirement, $qualiRequirement2, $qualiRequirement3] = $quali->requirements()->withPivot(['order', 'passed'])->get();
        $this->assertEquals($requirement->id, $qualiRequirement->id);
        $this->assertEquals(3, $qualiRequirement->pivot->order);
        $this->assertEquals(1, $qualiRequirement->pivot->passed);
        $this->assertEquals($requirement2->id, $qualiRequirement2->id);
        $this->assertEquals(5, $qualiRequirement2->pivot->order);
        $this->assertEquals(null, $qualiRequirement2->pivot->passed);
        $this->assertEquals($requirement3->id, $qualiRequirement3->id);
        $this->assertEquals(7, $qualiRequirement3->pivot->order);
        $this->assertEquals(null, $qualiRequirement3->pivot->passed);
        $observation = $quali->participant_observations()->withPivot(['order'])->first();
        $this->assertEquals($participantObservation->id, $observation->id);
        $this->assertEquals(2, $observation->pivot->order);
    }

    /**
     * @throws \App\Exceptions\RequirementsOutdatedException
     */
    public function test_appendRequirementsToQuali_shouldDoNothingWork_whenPassedSomethingOtherThanRequirements() {
        // given
        $quali = $this->createQuali();
        $course = $quali->participant->course;
        $participantObservation = $this->createParticipantObservation($course, $quali);
        $requirements = $course->requirements;
        $requirement = $requirements[0];
        $requirement2 = $requirements[1];
        $formatter = new TiptapFormatter($quali);
        $formatter->applyToQuali(['type' => 'doc', 'content' => [
            ['type' => 'heading', 'attrs' => ['level' => 5], 'content' => [['type' => 'text', 'text' => 'hello']]],
            ['type' => 'paragraph'],
            ['type' => 'observation', 'attrs' => ['id' => $participantObservation->id]],
            ['type' => 'requirement', 'attrs' => ['id' => $requirement->id, 'passed' => 1]],
            ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]],
        ]], false);

        // when
        $formatter->appendRequirementsToQuali(collect([$requirement2, $participantObservation]));

        // then
        $quali = Quali::find($quali->id);
        $this->assertEquals(4, $quali->contentNodes()->count());
        $this->assertEquals(1, $quali->participant_observations()->count());
        $this->assertEquals(2, $quali->requirements()->count());
        $nodes = $quali->contentNodes;
        $this->assertEquals(json_encode(['type' => 'heading', 'attrs' => ['level' => 5], 'content' => [['type' => 'text', 'text' => 'hello']]]), $nodes[0]->json);
        $this->assertEquals(0, $nodes[0]->order);
        $this->assertEquals(json_encode(['type' => 'paragraph']), $nodes[1]->json);
        $this->assertEquals(1, $nodes[1]->order);
        $this->assertEquals(json_encode(['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]]), $nodes[2]->json);
        $this->assertEquals(4, $nodes[2]->order);
        $this->assertEquals(json_encode(['type' => 'paragraph']), $nodes[3]->json);
        $this->assertEquals(6, $nodes[3]->order);
        [$qualiRequirement, $qualiRequirement2] = $quali->requirements()->withPivot(['order', 'passed'])->get();
        $this->assertEquals($requirement->id, $qualiRequirement->id);
        $this->assertEquals(3, $qualiRequirement->pivot->order);
        $this->assertEquals(1, $qualiRequirement->pivot->passed);
        $this->assertEquals($requirement2->id, $qualiRequirement2->id);
        $this->assertEquals(5, $qualiRequirement2->pivot->order);
        $this->assertEquals(null, $qualiRequirement2->pivot->passed);
        $observation = $quali->participant_observations()->withPivot(['order'])->first();
        $this->assertEquals($participantObservation->id, $observation->id);
        $this->assertEquals(2, $observation->pivot->order);
    }

    /**
     * @return Quali
     */
    protected function createQuali() {
        /** @var Course $course */
        $course = factory(Course::class)->create();
        /** @var QualiData $qualiData */
        $qualiData = $course->quali_datas()->create(['name' => 'Testquali']);
        /** @var Quali $quali */
        $quali = $qualiData->qualis()->create(['participant_id' => $course->participants()->first()->id]);
        return $quali;
    }

    /**
     * @param Course $course
     * @param Quali $quali
     * @return ParticipantObservation
     */
    public function createParticipantObservation(Course $course, Quali $quali) {
        /** @var Observation $observation */
        $observation = Observation::create(['content' => 'hat gut aufgepasst', 'impression' => 1, 'block' => $course->blocks()->first(), 'user_id' => $course->users()->first()->id]);
        $quali->participant->observations()->attach($observation);
        /** @var ParticipantObservation $participantObservation */
        $participantObservation = $quali->participant->participant_observations()->first();
        return $participantObservation;
    }
}
