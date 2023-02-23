<?php

namespace Tests\Feature\Admin\Block;

use App\Exceptions\ECamp2BlockOverviewParsingException;
use App\Exceptions\Handler;
use App\Exceptions\UnsupportedFormatException;
use App\Models\Block;
use App\Models\Observation;
use App\Services\Import\SpreadsheetReaderFactory;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\TestResponse;
use Mockery;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Tests\ReadsSpreadsheets;
use Tests\TestCaseWithCourse;

class ImportBlocksTest extends TestCaseWithCourse {

    use ReadsSpreadsheets;

    private $payload;

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
        $uploadedFile = $this->setUpInputFile('Blockuebersicht.xls');
        $this->payload = ['file' => $uploadedFile, 'source' => 'eCamp2BlockOverview'];

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldImportBlocks() {
        // given
        $uploadedFile = $this->setUpInputFile('Blockuebersicht.xls');
        $this->payload = ['file' => $uploadedFile, 'source' => 'eCamp2BlockOverview'];

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/blocks');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSeeInOrder([
            'In der importierten Datei wurden 4 Blöcke gefunden.',
            'Samstag 21.03.2026',
            '1.1', 'Erster Block',
            '1.2', 'Zweiter Block am gleichen Tag',
            'Sonntag 22.03.2026',
            '2.1', 'Dritter Block am n\u00e4chsten Tag',
            '10.10', 'Mehrstellige Blocknummer'
        ]);
    }

    public function test_shouldCropOverlyLongBlockNames() {
        // given
        $uploadedFile = $this->setUpInputFile('Blockuebersicht-longBlockName.xls');
        $this->payload = ['file' => $uploadedFile, 'source' => 'eCamp2BlockOverview'];

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/blocks');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Zweiter Block am gleichen Tag und mit extrem langem Blocknamen, so lang dass der Blockname gar nicht in die entsprechende Datenbank-Spalte in Qualix hineinpassen will, denn diese ist auf zweihundertundf\u00fcnfundf\u00fcnfzig Zeichen limitiert w\u00e4hrend dieser Blockn"');
    }

    public function test_shouldShowMessage_whenNoBlocksInImportedFile() {
        // given
        $uploadedFile = $this->setUpInputFile('Blockuebersicht-empty.xls');
        $this->payload = ['file' => $uploadedFile, 'source' => 'eCamp2BlockOverview'];

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
        $existingObservationId = Observation::create(['user_id' => $this->user()->id, 'block' => $blockId, 'content' => 'something', 'impression' => 0, 'participants' => [$participantId]])->id;
        $uploadedFile = $this->setUpInputFile('Blockuebersicht.xls');
        $this->payload = ['file' => $uploadedFile, 'source' => 'eCamp2BlockOverview'];

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/blocks');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertDontSee('Existierender Block');
        $response->assertDontSee('09.09.2009');
        $response->assertSeeInOrder(['Samstag 21.03.2026', '1.1', 'Erster Block', '1', '1.2', 'Zweiter Block am gleichen Tag', '0']);
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
        $response->assertOk();
        $response->assertSee('In deinem Kurs sind bereits Blöcke definiert. Wenn beim Import eine Blocknummer schon existiert, wird der bestehende Block durch den Import aktualisiert.');
    }

    public function test_formShouldNotShowMessage_whenNoBlocksExistInCourse() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/blocks/import');

        // then
        $response->assertOk();
        $response->assertDontSee('In deinem Kurs sind bereits Blöcke definiert. Wenn beim Import eine Blocknummer schon existiert, wird der bestehende Block durch den Import aktualisiert.');
    }

    public function test_shouldSupportXLSX() {
        // given
        $uploadedFile = $this->setUpInputFile('Blockuebersicht.xlsx', new Xlsx());
        $this->payload = ['file' => $uploadedFile, 'source' => 'eCamp2BlockOverview'];

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/blocks');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSeeInOrder([
            'In der importierten Datei wurden 4 Blöcke gefunden.',
            'Samstag 21.03.2026',
            '1.1', 'Erster Block',
            '1.2', 'Zweiter Block am gleichen Tag',
            'Sonntag 22.03.2026',
            '2.1', 'Dritter Block am n\u00e4chsten Tag',
            '10.10', 'Mehrstellige Blocknummer'
        ]);
    }

    public function test_shouldSupportCSV() {
        // given
        $csvReader = (new Csv())->setInputEncoding('CP1252');
        $uploadedFile = $this->setUpInputFile('Blockuebersicht.csv', $csvReader);
        $this->payload = ['file' => $uploadedFile, 'source' => 'eCamp2BlockOverview'];

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/blocks');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSeeInOrder([
            'In der importierten Datei wurden 4 Blöcke gefunden.',
            'Samstag 21.03.2026',
            '1.1', 'Erster Block',
            '1.2', 'Zweiter Block am gleichen Tag',
            'Sonntag 22.03.2026',
            '2.1', 'Dritter Block am n\u00e4chsten Tag',
            '10.10', 'Mehrstellige Blocknummer'
        ]);
    }

    public function test_shouldReportInvalidFormatToTheUser() {
        // given
        $this->get('/course/' . $this->courseId . '/admin/blocks/import');
        $factoryMock = Mockery::mock(SpreadsheetReaderFactory::class, function ($mock) {
            $mock->shouldReceive('getReader')->andThrow(new UnsupportedFormatException());
        });
        $uploadedFile = $this->setUpInputFile('Blockuebersicht.xlsx', new Xlsx());
        $this->payload = ['file' => $uploadedFile, 'source' => 'eCamp2BlockOverview'];

        $this->instance(SpreadsheetReaderFactory::class, $factoryMock);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/blocks/import');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Das Dateiformat der Block\u00fcbersicht ist nicht unterst\u00fctzt.');
    }

    public function test_shouldReportParsingErrorToTheUser() {
        // given
        $this->get('/course/' . $this->courseId . '/admin/blocks/import');
        $factoryMock = Mockery::mock(SpreadsheetReaderFactory::class, function ($mock) {
            $mock->shouldReceive('getReader')->andThrow(new ECamp2BlockOverviewParsingException('test exception'));
        });
        $uploadedFile = $this->setUpInputFile('Blockuebersicht.xlsx', new Xlsx());
        $this->payload = ['file' => $uploadedFile, 'source' => 'eCamp2BlockOverview'];

        $this->instance(SpreadsheetReaderFactory::class, $factoryMock);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/blocks/import');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('test exception');
    }

    public function test_shouldReportUnknownErrorToTheUserAndToSentry() {
        // given
        $this->get('/course/' . $this->courseId . '/admin/blocks/import');
        $factoryMock = Mockery::mock(SpreadsheetReaderFactory::class, function ($mock) {
            $mock->shouldReceive('getReader')->andThrow(new \RuntimeException('test runtime exception'));
        });
        $uploadedFile = $this->setUpInputFile('Blockuebersicht.xlsx', new Xlsx());
        $this->payload = ['file' => $uploadedFile, 'source' => 'eCamp2BlockOverview'];

        $this->instance(SpreadsheetReaderFactory::class, $factoryMock);
        $this->mock(Handler::class, function ($mock) {
            $mock->shouldReceive('report')->once();
        });

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/blocks/import');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Beim Import ist ein Fehler aufgetreten. Versuche es nochmals, oder erfasse deine Blöcke manuell.');
    }
}
