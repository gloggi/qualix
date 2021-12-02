<?php

namespace Tests\Feature\QualiContent;

use App\Models\Quali;
use App\Services\TiptapFormatter;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Mockery;
use PHPUnit\Framework\Constraint\IsEqual;
use Tests\TestCaseWithBasicData;

class ReadQualiContentTest extends TestCaseWithBasicData {

    protected $payload;
    protected $qualiId;
    protected $requirementId;

    public function setUp(): void {
        parent::setUp();

        $this->qualiId = $this->createQuali('Zwischequali');
        $quali = Quali::find($this->qualiId);
        $this->requirementId = $this->createRequirement();
        $quali->requirements()->attach([$this->requirementId => ['passed' => null, 'order' => 10]]);
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $this->participantId . '/qualis/' . $this->qualiId . '/edit');

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldWork() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $this->participantId . '/qualis/' . $this->qualiId . '/edit');

        // then
        $response->assertOk();
    }

    public function test_shouldOrderObservationsByBlock() {
        // given
        $obs0 = $this->createObservation('First observation', 1, [], [], $this->blockId);
        $obs1 = $this->createObservationInBlock('later date', '1.1', '02.01.2019');
        $obs2 = $this->createObservationInBlock('earlier date', '1.1', '31.12.2018');
        $obs3 = $this->createObservationInBlock('later day number', '2.1', '01.01.2019');
        $obs4 = $this->createObservationInBlock('earlier day number', '0.1', '01.01.2019');
        $obs5 = $this->createObservationInBlock('two-digit day number', '11.1', '01.01.2019');
        $obs6 = $this->createObservationInBlock('later block number', '1.2', '01.01.2019');
        $obs7 = $this->createObservationInBlock('earlier block number', '1.0', '01.01.2019');
        $obs8 = $this->createObservationInBlock('two-digit block number', '1.12', '01.01.2019');
        $obs9 = $this->createObservationInBlock('Block 2 later block name', '1.1', '01.01.2019');
        $obs10 = $this->createObservationInBlock('Block 0 earlier block name', '1.1', '01.01.2019');
        $expected = [$obs2, $obs4, $obs7, $obs10, $obs0, $obs9, $obs6, $obs8, $obs3, $obs5, $obs1];

        // when
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $this->participantId . '/qualis/' . $this->qualiId . '/edit');

        // then
        $observations = Arr::get($response->original->gatherData(), 'observations')->map->id->all();
        $this->assertThat($observations, $this->equalTo($expected));
    }

    protected function createObservationInBlock($name, $blockNumber, $blockDate) {
        $blockId = $this->createBlock($name, $blockNumber, $blockDate);
        return $this->createObservation($name, 1, [], [], $blockId);
    }
}
