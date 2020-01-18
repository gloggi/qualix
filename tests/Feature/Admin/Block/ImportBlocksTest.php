<?php

namespace Tests\Feature\Admin\Block;

use App\Models\Block;
use App\Models\Observation;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\UploadedFile;
use Mockery;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use Tests\TestCaseWithCourse;

class ImportBlocksTest extends TestCaseWithCourse {

    private $payload;

    public function setUp(): void {
        parent::setUp();

        $uploadedFile = Mockery::mock(UploadedFile::class, function ($mock) {
            $mock->shouldReceive('isValid')->andReturn(true);
            $mock->shouldReceive('getPath')->andReturn('/some/path');
            $mock->shouldReceive('getSize')->andReturn(1024);
            $mock->shouldReceive('getRealPath')->andReturn('/some/path');
        });
        $this->payload = ['file' => $uploadedFile, 'source' => 'eCamp2BlockOverview'];
    }

    public function test_viewingForm_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/blocks/import');

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_importingBlocks_shouldRequireLogin() {
        // given
        auth()->logout();
        $this->setUpInputFile('Blockuebersicht.xls');

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldImportBlocks() {
        // given
        $this->setUpInputFile('Blockuebersicht.xls');

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/blocks');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSeeTextInOrder([
            'In der importierten Datei wurden 4 Blöcke gefunden.',
            'Samstag 21.03.2020',
            '1.1', 'Erster Block',
            '1.2', 'Zweiter Block am gleichen Tag',
            'Sonntag 22.03.2020',
            '2.1', 'Dritter Block am nächsten Tag',
            '10.10', 'Mehrstellige Blocknummer'
        ]);
    }

    public function test_shouldShowMessage_whenNoBlocksInImportedFile() {
        // given
        $this->setUpInputFile('Blockuebersicht-empty.xls');

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/blocks');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('In der importierten Datei wurden keine Blöcke gefunden.');
    }

    public function test_shouldUpdateExistingBlocks_whenBlockNumberMatches() {
        // given
        $blockId = $this->createBlock('Existierender Block', '1.1', '09.09.2009');
        $participantId = $this->createParticipant('Pflock');
        $existingObservationId = Observation::create(['user_id' => $this->user()->id, 'block_id' => $blockId, 'content' => 'something', 'impression' => 0, 'participant_ids' => [$participantId]])->id;
        $this->setUpInputFile('Blockuebersicht.xls');

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/blocks');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertDontSee('Existierender Block');
        $response->assertDontSee('09.09.2009');
        $response->assertSeeTextInOrder(['Samstag 21.03.2020', '1.1', 'Erster Block', '1', '1.2', 'Zweiter Block am gleichen Tag', '0']);
        $connectedObservations = Block::where('day_number', '=', '1')
            ->where('block_number', '=', '1')
            ->where('course_id', '=', $this->courseId)
            ->first()->observations();
        $this->assertEquals(1, $connectedObservations->count());
        $this->assertEquals($existingObservationId, $connectedObservations->first()->id);
    }

    public function test_formShouldShowMessage_whenBlocksExistInCourse() {
        // given
        $this->createBlock();

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/blocks/import');

        // then
        $response->assertStatus(200);
        $response->assertSee('In deinem Kurs sind bereits Blöcke definiert. Wenn beim Import eine Blocknummer schon existiert, wird der bestehende Block durch den Import aktualisiert.');
    }

    public function test_formShouldNotShowMessage_whenNoBlocksExistInCourse() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/blocks/import');

        // then
        $response->assertStatus(200);
        $response->assertDontSee('In deinem Kurs sind bereits Blöcke definiert. Wenn beim Import eine Blocknummer schon existiert, wird der bestehende Block durch den Import aktualisiert.');
    }

    protected function setUpInputFile($filename) {
        $this->instance(Xls::class, Mockery::mock(Xls::class, function ($mock) use($filename) {
            $mock->shouldReceive('load')->andReturn((new Xls())->load(__DIR__.'/../../../resources/' . $filename));
        })->makePartial());
    }
}
