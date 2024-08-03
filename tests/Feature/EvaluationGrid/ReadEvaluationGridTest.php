<?php

namespace Tests\Feature\EvaluationGrid;

use App\Models\Block;
use App\Models\Course;
use App\Models\EvaluationGrid;
use App\Models\EvaluationGridTemplate;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Tests\TestCaseWithBasicData;

class ReadEvaluationGridTest extends TestCaseWithBasicData {

    private $evaluationGridTemplateId;

    private $evaluationGridId;

    public function setUp(): void {
        parent::setUp();

        $this->evaluationGridTemplateId = $this->createEvaluationGridTemplate();
        $this->evaluationGridId = $this->createEvaluationGrid($this->evaluationGridTemplateId);
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->get('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldRequireNonArchivedCourse() {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->get('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldDisplayEvaluationGrid() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId);

        // then
        $response->assertOk();
        $response->assertSee(EvaluationGridTemplate::find($this->evaluationGridTemplateId)->name);
    }

    public function test_shouldNotDisplayEvaluationGrid_fromOtherCourseOfSameUser() {
        // given
        $otherKursId = $this->createCourse('Zweiter Kurs', '');

        // when
        $response = $this->get('/course/' . $otherKursId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldNotDisplayEvaluationGrid_fromOtherCourse() {
        // given
        $otherCourseId = $this->createCourse('Zweiter Kurs', '', false);
        $this->createBlock('Block 1', '1.1', '01.01.2019', null, $otherCourseId);
        $otherUserId = $this->createUser(['name' => 'Lindo'])->id;
        Course::find($otherCourseId)->users()->attach($otherUserId);
        $otherEvaluationGridTemplateId = $this->createEvaluationGridTemplate('Unternehmungsplanung', $otherCourseId);
        $otherEvaluationGridId = $this->createEvaluationGrid($otherEvaluationGridTemplateId);

        // when
        $response = $this->get('/course/' . $otherCourseId . '/evaluation_grid/' . $otherEvaluationGridTemplateId . '/' . $otherEvaluationGridId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldRenderOnAllParticipants_whenMultipleAreAssigned() {
        // given
        $otherParticipantId = $this->createParticipant('Zweiter TN<em>yay!</em>');
        $evaluationGridTemplateId2 = $this->createEvaluationGridTemplate('Wird auf allen TN angezeigt');
        $evaluationGridId2 = $this->createEvaluationGrid($evaluationGridTemplateId2);
        EvaluationGrid::find($evaluationGridId2)->participants()->sync([$this->participantId, $otherParticipantId]);

        // when
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $this->participantId);

        // then
        $response->assertOk();
        $response->assertSee('"Wird auf allen TN angezeigt"');

        // when
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $otherParticipantId);

        // then
        $response->assertOk();
        $response->assertSee('"Wird auf allen TN angezeigt"');
    }

    public function test_shouldOrderEvaluationGridsByBlockOrder() {
        // given
        $this->createBlock('later date', '1.1', '02.01.2019');
        $this->createBlock('earlier date', '1.1', '31.12.2018');
        $this->createBlock('later day number', '2.1', '01.01.2019');
        $this->createBlock('earlier day number', '0.1', '01.01.2019');
        $this->createBlock('two-digit day number', '11.1', '01.01.2019');
        $this->createBlock('later block number', '1.2', '01.01.2019');
        $this->createBlock('earlier block number', '1.0', '01.01.2019');
        $this->createBlock('two-digit block number', '1.12', '01.01.2019');
        $this->createBlock('Block 2 later block name', '1.1', '01.01.2019');
        $this->createBlock('Block 0 earlier block name', '1.1', '01.01.2019');
        /** @var Collection $blockIds */
        $blockIds = $this->user()->lastAccessedCourse->blocks->map(function (Block $block) { return $block->id; });
        $blockIdsToCreateObservations = $blockIds->sort();
        $blockIdsToCreateObservations->shift();
        foreach ($blockIdsToCreateObservations as $blockId) {
            $evaluationGridTemplateId = $this->createEvaluationGridTemplate(Block::find($blockId)->name);
            $this->createEvaluationGrid($evaluationGridTemplateId, $blockId);
        }

        // when
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $this->participantId);

        // then
        $response->assertOk();
        $response->assertSeeInOrder([
          'earlier date',
          'earlier day number',
          'earlier block number',
          'Block 0 earlier block name',
          'Unternehmungsplanung',
          'Block 2 later block name',
          'later block number',
          'two-digit block number',
          'later day number',
          'two-digit day number',
          'later date',
        ]);
    }
}
